<?php

namespace App\Livewire\Organisations;

use App\Concerns\InteractsWithPhoneNumbers;
use App\Models\Organisation;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class StaffManager extends Component
{
    use InteractsWithPhoneNumbers;

    public Organisation $organisation;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string')]
    public string $phoneCountryCode = '254';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('required|string')]
    public string $role = 'Agent';

    /**
     * The member currently being edited in the "Edit role" modal.
     */
    public ?int $editingUserId = null;

    public string $editingUserName = '';

    // Note: no #[Validate] attribute here on purpose. The shared $this->validate()
    // call in addStaff() validates every attributed property, so this field is
    // validated explicitly in updateRole() instead to avoid leaking a phantom
    // "role required" error into the unrelated "Add staff" form.
    public string $editingRole = '';

    /**
     * The member queued for removal in the confirmation modal.
     */
    public ?int $removingUserId = null;

    public string $removingUserName = '';

    /**
     * Bind the organisation passed from the parent settings page.
     */
    public function mount(Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    /**
     * Keep the permission team scope aligned with this organisation.
     */
    public function boot(): void
    {
        setPermissionsTeamId($this->organisation->getKey());
    }

    /**
     * The roles that can be assigned to staff (every default role except Owner).
     *
     * @return array<int, string>
     */
    #[Computed]
    public function assignableRoles(): array
    {
        $roles = array_map(strval(...), array_keys((array) config('organisation.roles', [])));

        return array_values(array_filter(
            $roles,
            fn (string $role) => $role !== 'Owner',
        ));
    }

    /**
     * Add a member of staff to the organisation, creating the account if needed.
     */
    public function addStaff(): void
    {
        $this->authorize('manage members');

        $validated = $this->validate();

        if (! in_array($validated['role'], $this->assignableRoles(), true)) {
            $this->addError('role', __('The selected role is invalid.'));

            return;
        }

        $organisation = $this->organisation;

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $this->normalisePhone($validated['phoneCountryCode'], $validated['phone']),
                'password' => Str::password(16),
            ],
        );

        if ($organisation->hasMember($user)) {
            $this->addError('email', __('This person is already a member of the organisation.'));

            return;
        }

        $organisation->members()->attach($user);

        setPermissionsTeamId($organisation->getKey());
        $user->syncRoles([$validated['role']]);

        $this->reset('name', 'email', 'phone');
        $this->role = 'Agent';

        // Tell the PowerGrid staff table to re-query.
        $this->dispatch('staff-added');

        Flux::modal('add-staff')->close();

        Flux::toast(variant: 'success', text: __(':name was added to the organisation.', ['name' => $user->name]));
    }

    /**
     * Open the "Edit role" modal for the given member.
     */
    #[On('staff-edit-role')]
    public function editRole(int $userId): void
    {
        $this->authorize('manage roles');

        $organisation = $this->organisation;

        if ($userId === $organisation->owner_id) {
            return;
        }

        $user = $organisation->members()->whereKey($userId)->first();

        if ($user === null) {
            return;
        }

        setPermissionsTeamId($organisation->getKey());

        $this->editingUserId = $user->getKey();
        $this->editingUserName = $user->name;
        $this->editingRole = $user->getRoleNames()->first() ?? '';
        $this->resetErrorBag(['editingRole']);

        Flux::modal('edit-staff-role')->show();
    }

    /**
     * Persist the newly selected role for the member being edited.
     */
    public function updateRole(): void
    {
        $this->authorize('manage roles');

        $organisation = $this->organisation;

        if ($this->editingUserId === null || $this->editingUserId === $organisation->owner_id) {
            return;
        }

        $this->validate(['editingRole' => ['required', 'string']]);

        if (! in_array($this->editingRole, $this->assignableRoles(), true)) {
            $this->addError('editingRole', __('The selected role is invalid.'));

            return;
        }

        $user = $organisation->members()->whereKey($this->editingUserId)->first();

        if ($user === null) {
            return;
        }

        setPermissionsTeamId($organisation->getKey());
        $user->syncRoles([$this->editingRole]);

        $this->reset('editingUserId', 'editingUserName', 'editingRole');

        // Tell the PowerGrid staff table to re-query the updated role.
        $this->dispatch('staff-added');

        Flux::modal('edit-staff-role')->close();

        Flux::toast(variant: 'success', text: __(":name's role was updated.", ['name' => $user->name]));
    }

    /**
     * Open the confirmation modal before removing a member.
     */
    #[On('staff-confirm-remove')]
    public function confirmRemove(int $userId): void
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

        $this->removingUserId = $user->getKey();
        $this->removingUserName = $user->name;

        Flux::modal('confirm-remove-staff')->show();
    }

    /**
     * Confirm the removal, delegating the actual detach to the staff table.
     */
    public function removeStaff(): void
    {
        $this->authorize('manage members');

        if ($this->removingUserId === null) {
            return;
        }

        // The detach logic lives in StaffTable; trigger it now that the action
        // has been confirmed.
        $this->dispatch('staff-remove', userId: $this->removingUserId);

        $this->reset('removingUserId', 'removingUserName');

        Flux::modal('confirm-remove-staff')->close();
    }

    public function render(): View
    {
        return view('livewire.organisations.staff-manager');
    }
}
