<?php

namespace App\Concerns;

use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Industry;
use App\Models\Organisation;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * Shared organisation profile form fields (industry, location, currency and
 * contact details) used by both the create wizard and the settings page. Keeps
 * the dropdown option lists, validation rules and country-driven defaults in a
 * single place.
 */
trait InteractsWithOrganisationProfile
{
    use InteractsWithPhoneNumbers;
    use WithFileUploads;

    /**
     * A newly selected logo upload (Livewire temporary file), if any.
     *
     * @var TemporaryUploadedFile|null
     */
    public $logo = null;

    public ?int $industryId = null;

    public ?int $countryId = null;

    public ?int $cityId = null;

    public ?string $currencyCode = null;

    public string $taxPin = '';

    public string $phoneCountryCode = '254';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    /**
     * Options for the industry dropdown.
     *
     * @return array<int, array{value: int, label: string}>
     */
    #[Computed]
    public function industries(): array
    {
        return Industry::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Industry $industry): array => ['value' => $industry->id, 'label' => $industry->name])
            ->all();
    }

    /**
     * Options for the country dropdown.
     *
     * @return array<int, array{value: int, label: string}>
     */
    #[Computed]
    public function countries(): array
    {
        return Country::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Country $country): array => ['value' => $country->id, 'label' => $country->name])
            ->all();
    }

    /**
     * Options for the city dropdown, scoped to the selected country.
     *
     * @return array<int, array{value: int, label: string}>
     */
    #[Computed]
    public function cities(): array
    {
        if ($this->countryId === null) {
            return [];
        }

        return City::query()
            ->where('country_id', $this->countryId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (City $city): array => ['value' => $city->id, 'label' => $city->name])
            ->all();
    }

    /**
     * Options for the currency dropdown (the code is the value).
     *
     * @return array<int, array{value: string, label: string}>
     */
    #[Computed]
    public function currencies(): array
    {
        return Currency::query()
            ->orderBy('code')
            ->get(['code', 'name'])
            ->map(fn (Currency $currency): array => [
                'value' => $currency->code,
                'label' => $currency->code.' — '.$currency->name,
            ])
            ->all();
    }

    /**
     * Keep the city and currency in step with the chosen country.
     */
    public function updatedCountryId(): void
    {
        $this->cityId = null;

        $country = Country::find($this->countryId);

        if ($country === null) {
            return;
        }

        // Default the currency to the country's, if we have it on file.
        if (filled($country->currency) && Currency::whereKey($country->currency)->exists()) {
            $this->currencyCode = $country->currency;
        }

        // Default the phone dial code to the country's.
        if (filled($country->phone_code)) {
            $this->phoneCountryCode = (string) $country->phone_code;
        }
    }

    /**
     * Validation rules for the profile fields.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function profileRules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'max:2048'],
            'industryId' => ['required', 'integer', 'exists:industries,id'],
            'countryId' => ['required', 'integer', 'exists:countries,id'],
            'cityId' => ['nullable', 'integer', Rule::exists('cities', 'id')->where('country_id', $this->countryId)],
            'currencyCode' => ['nullable', 'string', 'exists:currencies,code'],
            'taxPin' => ['nullable', 'string', 'max:50'],
            'phoneCountryCode' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Human-friendly names used in profile validation messages.
     *
     * @return array<string, string>
     */
    protected function profileValidationAttributes(): array
    {
        return [
            'logo' => __('logo'),
            'industryId' => __('industry'),
            'countryId' => __('country'),
            'cityId' => __('city'),
            'currencyCode' => __('currency'),
            'taxPin' => __('tax PIN'),
            'phone' => __('phone'),
            'email' => __('email'),
            'address' => __('address'),
        ];
    }

    /**
     * Populate the profile fields from an existing organisation.
     */
    protected function fillProfileFrom(Organisation $organisation): void
    {
        $this->industryId = $organisation->industry_id;
        $this->countryId = $organisation->country_id;
        $this->cityId = $organisation->city_id;
        $this->currencyCode = $organisation->currency_code;
        $this->taxPin = (string) $organisation->tax_pin;
        $this->email = (string) $organisation->email;
        $this->address = (string) $organisation->address;

        [$this->phoneCountryCode, $this->phone] = $this->splitPhone($organisation->phone, $this->phoneCountryCode);
    }

    /**
     * The profile fields shaped for mass assignment onto an organisation.
     *
     * @return array<string, mixed>
     */
    protected function profileAttributes(): array
    {
        return [
            'industry_id' => $this->industryId,
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'currency_code' => $this->currencyCode ?: null,
            'tax_pin' => $this->taxPin ?: null,
            'phone' => $this->phone !== '' ? $this->normalisePhone($this->phoneCountryCode, $this->phone) : null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
        ];
    }

    /**
     * Store a freshly uploaded logo on the organisation, if one was selected.
     */
    protected function persistLogo(Organisation $organisation): void
    {
        if ($this->logo !== null) {
            $organisation->updateProfilePhoto($this->logo, 'organisation-logos');
            $this->reset('logo');
        }
    }
}
