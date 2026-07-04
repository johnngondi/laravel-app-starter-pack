<?php

namespace App\Livewire\Settings;

use App\Concerns\InteractsWithPhoneNumbers;
use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Profile settings')]
class Profile extends Component
{
    use InteractsWithPhoneNumbers, ProfileValidationRules, WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phoneCountryCode = '254';

    public string $phone = '';

    public string $tax_pin = '';

    /**
     * A newly selected profile photo upload (Livewire temporary file), if any.
     *
     * @var TemporaryUploadedFile|null
     */
    public $photo = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->tax_pin = $user->tax_pin ?? '';

        [$this->phoneCountryCode, $this->phone] = $this->splitPhone($user->phone);
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'phoneCountryCode' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tax_pin' => $this->taxPinRules(),
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalisePhone($validated['phoneCountryCode'], $validated['phone']),
            'tax_pin' => $validated['tax_pin'] ?: null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($this->photo !== null) {
            $user->updateProfilePhoto($this->photo);
            $this->reset('photo');
        }

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Remove the current user's profile photo.
     */
    public function removePhoto(): void
    {
        Auth::user()->deleteProfilePhoto();
        $this->reset('photo');

        Flux::toast(variant: 'success', text: __('Profile photo removed.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        $user = Auth::user();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        $user = Auth::user();

        return ! $user instanceof MustVerifyEmail || $user->hasVerifiedEmail();
    }
}
