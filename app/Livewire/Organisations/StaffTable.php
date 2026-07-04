<?php

namespace App\Livewire\Organisations;

use App\Models\Organisation;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\Rules\BaseRule;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Footer;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class StaffTable extends PowerGridComponent
{
    public string $tableName = 'staff-table';

    public Organisation $organisation;

    /**
     * @return array<int, Header|Footer>
     */
    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    /**
     * @return Builder<User>
     */
    public function datasource(): Builder
    {
        setPermissionsTeamId($this->organisation->getKey());

        // Members of this organisation, via the organisation_user pivot. A plain
        // whereIn (rather than the relation's join) keeps the query clean for
        // PowerGrid's column selects and sorting.
        return User::query()
            ->whereIn('users.id', function (QueryBuilder $query): void {
                $query->select('user_id')
                    ->from('organisation_user')
                    ->where('organisation_id', $this->organisation->getKey());
            });
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('phone', fn (User $user) => $user->phone ?: '—')
            ->add('role', fn (User $user) => $user->getRoleNames()->first() ?? '—')
            ->add('joined', fn (User $user) => $user->created_at?->format('M j, Y'));
    }

    /**
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__('Name'), 'name')
                ->searchable()
                ->sortable(),

            Column::make(__('Email'), 'email')
                ->searchable()
                ->sortable(),

            Column::make(__('Phone'), 'phone')
                ->searchable(),

            Column::make(__('Role'), 'role'),

            Column::make(__('Joined'), 'joined', 'created_at')
                ->sortable(),

            Column::action(__('Actions')),
        ];
    }

    /**
     * @return array<int, Button>
     */
    public function actions(User $row): array
    {
        return [
            Button::add('edit-role')
                ->slot(__('Edit role'))
                ->class('inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800 cursor-pointer')
                ->dispatch('staff-edit-role', ['userId' => $row->id]),

            Button::add('remove')
                ->slot(__('Remove'))
                ->class('inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium text-negative-600 hover:bg-negative-50 dark:text-negative-400 dark:hover:bg-negative-950/40 cursor-pointer')
                ->dispatch('staff-confirm-remove', ['userId' => $row->id]),
        ];
    }

    /**
     * @return array<int, BaseRule>
     */
    public function actionRules(User $row): array
    {
        return [
            // The organisation owner's role is fixed and cannot be reassigned.
            Rule::button('edit-role')
                ->hide()
                ->when(fn (User $row): bool => (int) $row->id === (int) $this->organisation->owner_id),

            // The organisation owner cannot be removed.
            Rule::button('remove')
                ->hide()
                ->when(fn (User $row): bool => (int) $row->id === (int) $this->organisation->owner_id),
        ];
    }

    /**
     * Re-render (and therefore re-query) when the parent adds a new member.
     * Named to avoid clashing with PowerGrid's own refresh internals.
     */
    #[On('staff-added')]
    public function onStaffAdded(): void
    {
        // No body needed — handling the event triggers a Livewire re-render.
    }

    #[On('staff-remove')]
    public function removeStaff(int $userId): void
    {
        $this->authorize('manage members');

        $organisation = $this->organisation;

        if ($userId === $organisation->owner_id) {
            return;
        }

        $user = $organisation->members()->whereKey($userId)->first();

        if ($user === null) {
            return;
        }

        setPermissionsTeamId($organisation->getKey());
        $user->syncRoles([]);

        $organisation->members()->detach($user);

        if ($user->current_organisation_id === $organisation->getKey()) {
            $user->forceFill(['current_organisation_id' => null])->save();
        }

        Flux::toast(variant: 'success', text: __('Member removed.'));
    }
}
