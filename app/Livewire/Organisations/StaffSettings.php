<?php

namespace App\Livewire\Organisations;

use App\Models\Organisation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Organisation · Staff')]
class StaffSettings extends Component
{
    public Organisation $organisation;

    /**
     * Bind the organisation resolved from the route slug.
     */
    public function mount(Organisation $organisation): void
    {
        $this->authorize('manage members');

        $this->organisation = $organisation;
    }

    /**
     * Keep the permission team scope aligned with this organisation.
     */
    public function boot(): void
    {
        setPermissionsTeamId($this->organisation->getKey());
    }

    public function render(): View
    {
        return view('livewire.organisations.staff-settings');
    }
}
