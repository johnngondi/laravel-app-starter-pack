<?php

namespace App\Livewire\Organisations;

use App\Concerns\InteractsWithOrganisationProfile;
use App\Models\City;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Organisation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.onboarding')]
#[Title('Create organisation')]
class CreateOrganisation extends Component
{
    use InteractsWithOrganisationProfile;

    /**
     * The current wizard step (1-based).
     */
    public int $step = 1;

    public string $name = '';

    /**
     * Validation rules for every field, keyed by property.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            ...$this->profileRules(),
        ];
    }

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
     * The wizard steps, in order, keyed by their labels.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function steps(): array
    {
        return [
            __('Organisation'),
            __('Contact'),
            __('Review'),
        ];
    }

    /**
     * Read-only summary shown on the review step (empty values omitted).
     *
     * @return array<string, string>
     */
    #[Computed]
    public function review(): array
    {
        return array_filter([
            __('Organisation name') => $this->name,
            __('Industry') => Industry::find($this->industryId)?->name,
            __('Country') => Country::find($this->countryId)?->name,
            __('City') => City::find($this->cityId)?->name,
            __('Currency') => $this->currencyCode,
            __('Tax PIN') => $this->taxPin,
            __('Phone') => $this->phone !== '' ? '+'.$this->phoneCountryCode.' '.$this->phone : null,
            __('Email') => $this->email,
            __('Address') => $this->address,
        ], fn (?string $value): bool => filled($value));
    }

    /**
     * Handle a step's form submission: advance through the wizard, or create
     * the organisation once the final step is reached.
     */
    public function submit(): void
    {
        if ($this->step < count($this->steps())) {
            $this->nextStep();

            return;
        }

        $this->create();
    }

    /**
     * Advance to the next step after validating the current one's fields.
     */
    public function nextStep(): void
    {
        $this->validateStep();

        $this->step = min($this->step + 1, count($this->steps()));
    }

    /**
     * Return to the previous step.
     */
    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    /**
     * Validate only the fields that belong to the current step.
     */
    protected function validateStep(): void
    {
        $fields = match ($this->step) {
            1 => ['name', 'logo', 'industryId', 'countryId', 'cityId', 'currencyCode', 'taxPin'],
            2 => ['phoneCountryCode', 'phone', 'email', 'address'],
            default => [],
        };

        if ($fields !== []) {
            $this->validate(Arr::only($this->rules(), $fields));
        }
    }

    /**
     * Create a new organisation owned by the current user and switch to it.
     */
    public function create(): void
    {
        $this->validate();

        $organisation = Organisation::createForOwner(Auth::user(), $this->name, $this->profileAttributes());

        $this->persistLogo($organisation);

        $this->redirectRoute('organisation.dashboard', ['organisation' => $organisation], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organisations.create-organisation');
    }
}
