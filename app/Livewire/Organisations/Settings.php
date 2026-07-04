<?php

namespace App\Livewire\Organisations;

use App\Concerns\InteractsWithOrganisationProfile;
use App\Concerns\PasswordValidationRules;
use App\Models\Organisation;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Organisation · General')]
class Settings extends Component
{
    use InteractsWithOrganisationProfile;
    use PasswordValidationRules;

    public Organisation $organisation;

    public string $name = '';

    public string $password = '';

    /**
     * Human-friendly names used in validation messages.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => __('organisation name'),
            ...$this->profileValidationAttributes(),
        ];
    }

    /**
     * Bind the organisation resolved from the route slug and populate the form.
     */
    public function mount(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->name = $organisation->name;

        $this->fillProfileFrom($organisation);
    }

    /**
     * Keep the permission team scope aligned with this organisation.
     */
    public function boot(): void
    {
        setPermissionsTeamId($this->organisation->getKey());
    }

    /**
     * Update the organisation's general details.
     */
    public function updateOrganisation(): void
    {
        $this->authorize('update organisation');

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            ...$this->profileRules(),
        ]);

        $this->organisation->update([
            'name' => $this->name,
            ...$this->profileAttributes(),
        ]);

        $this->persistLogo($this->organisation);

        Flux::toast(variant: 'success', text: __('Organisation updated.'));
    }

    /**
     * Remove the organisation's logo.
     */
    public function removeLogo(): void
    {
        $this->authorize('update organisation');

        $this->organisation->deleteProfilePhoto();
        $this->reset('logo');

        Flux::toast(variant: 'success', text: __('Logo removed.'));
    }

    /**
     * Permanently delete the organisation and everything tied to it after
     * confirming the owner's password.
     */
    public function deleteOrganisation(): void
    {
        $this->authorize('delete organisation');

        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        DB::transaction(fn () => $this->organisation->delete());

        // current_organisation_id is nulled by the database cascade; the
        // dashboard route bounces to a remaining organisation or onboarding.
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organisations.settings');
    }
}
