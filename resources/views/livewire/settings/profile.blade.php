<section class="w-full">
    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.user-layout :heading="__('Profile')" :subheading="__('Update your name, contact details and billing tax PIN')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <x-avatar-upload
                :label="__('Profile photo')"
                wire-model="photo"
                :preview-url="auth()->user()->profile_photo_path ? auth()->user()->profile_photo_url : null"
                :has-photo="(bool) auth()->user()->profile_photo_path"
                remove-action="removePhoto"
            />

            <x-input wire:model="name" type="text" required autofocus autocomplete="name">
                <x-slot:label><x-required-label>{{ __('Name') }}</x-required-label></x-slot:label>
            </x-input>

            <div>
                <x-input wire:model="email" type="email" required autocomplete="email">
                    <x-slot:label><x-required-label>{{ __('Email') }}</x-required-label></x-slot:label>
                </x-input>

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                    </div>
                @endif
            </div>

            <x-phone-field :label="__('Phone')" wire-model="phone" code-wire-model="phoneCountryCode" />

            <x-input
                wire:model="tax_pin"
                :label="__('Tax PIN')"
                type="text"
                autocomplete="off"
                :placeholder="__('Optional — used for billing')"
            />

            <div class="flex items-center gap-4">
                <x-button primary type="submit" spinner="updateProfileInformation">{{ __('Save') }}</x-button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.user-layout>
</section>
