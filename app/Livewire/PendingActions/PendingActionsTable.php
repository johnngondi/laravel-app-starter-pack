<?php

namespace App\Livewire\PendingActions;

use App\Actions\PendingActions\DeletePendingTaskAction;
use App\Models\PendingAction;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Footer;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class PendingActionsTable extends PowerGridComponent
{
    public string $tableName = 'pending-actions-table';

    public string $sortField = 'due_at';

    public string $sortDirection = 'asc';

    /** Id of the task currently awaiting "mark as done" confirmation. */
    public ?int $confirmingId = null;

    public function noDataLabel(): string
    {
        return __('Hooray! No pending items.');
    }

    /**
     * @return array<int, Header|Footer>
     */
    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage(50, [50, 100, 200])
                ->showRecordCount()
                ->includeViewOnBottom('livewire.pending-actions.confirm-done-modal'),
        ];
    }

    /**
     * @return Builder<PendingAction>
     */
    public function datasource(): Builder
    {
        // Tasks where the signed-in user is an eligible actor.
        return Auth::user()->pendingActions();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('notes')
            ->add('action_type')
            ->add('priority_badge', fn (PendingAction $row) => view('livewire.pending-actions.priority-badge', ['row' => $row])->render())
            ->add('due_at_formatted', fn (PendingAction $row) => $row->due_at->format('M j, Y g:i A'))
            ->add('actions_cell', fn (PendingAction $row) => view('livewire.pending-actions.actions', ['row' => $row])->render());
    }

    /**
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__('Notes'), 'notes')
                ->searchable()
                ->sortable(),

            Column::make(__('Type'), 'action_type')
                ->searchable()
                ->sortable(),

            Column::make(__('Priority'), 'priority_badge', 'priority')
                ->sortable(),

            Column::make(__('Due'), 'due_at_formatted', 'due_at')
                ->sortable(),

            Column::make(__('Actions'), 'actions_cell'),
        ];
    }

    /**
     * Ask for confirmation before resolving the task (warning confirm modal).
     */
    public function confirmDone(int $id): void
    {
        $this->confirmingId = $id;

        Flux::modal('confirm-pending-done')->show();
    }

    /**
     * Resolve the confirmed task. Once any actor acts, it is removed for all.
     */
    public function markAsDone(): void
    {
        if ($this->confirmingId === null) {
            return;
        }

        $pendingAction = Auth::user()->pendingActions()
            ->whereKey($this->confirmingId)
            ->first();

        $this->confirmingId = null;
        Flux::modal('confirm-pending-done')->close();

        if ($pendingAction === null) {
            return;
        }

        app(DeletePendingTaskAction::class)->handle($pendingAction);

        Flux::toast(variant: 'success', text: __('Task marked as done.'));

        // Let the page know the count changed (e.g. to refresh the sidebar badge).
        $this->dispatch('pending-actions-updated');
    }
}
