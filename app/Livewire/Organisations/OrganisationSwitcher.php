<?php

namespace App\Livewire\Organisations;

use App\Models\Organisation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrganisationSwitcher extends Component
{
    /**
     * The organisations the current user belongs to.
     *
     * @return Collection<int, Organisation>
     */
    #[Computed]
    public function organisations(): Collection
    {
        return Auth::user()->organisations()->orderBy('name')->get();
    }

    /**
     * The user's currently active organisation.
     */
    #[Computed]
    public function current(): ?Organisation
    {
        return Auth::user()->currentOrganisation;
    }

    /**
     * Switch the active organisation and reload into its dashboard.
     */
    public function switch(int $organisationId): void
    {
        $organisation = Auth::user()->organisations()->whereKey($organisationId)->first();

        if ($organisation === null) {
            return;
        }

        Auth::user()->switchOrganisation($organisation);

        $this->redirectRoute('organisation.dashboard', ['organisation' => $organisation], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organisations.organisation-switcher');
    }
}
