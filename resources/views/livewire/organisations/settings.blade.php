<section class="w-full">
    <x-settings.layout
        :heading="__('General')"
        :subheading="__('Manage :name and its details.', ['name' => $organisation->name])"
    >
        @can('update organisation')
            <form wire:submit="updateOrganisation" class="w-full max-w-lg space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-avatar-upload
                        class="sm:col-span-2"
                        :label="__('Logo')"
                        wire-model="logo"
                        shape="rounded"
                        :preview-url="$organisation->profile_photo_path ? $organisation->profile_photo_url : null"
                        :has-photo="(bool) $organisation->profile_photo_path"
                        remove-action="removeLogo"
                    />

                    <x-input wire:model="name" type="text" required>
                        <x-slot:label><x-required-label>{{ __('Organisation name') }}</x-required-label></x-slot:label>
                    </x-input>

                    <x-select
                        wire:model="industryId"
                        :options="$this->industries"
                        option-label="label"
                        option-value="value"
                        :clearable="false"
                        min-items-for-search="1"
                        :placeholder="__('Select an industry')"
                    >
                        <x-slot:label><x-required-label>{{ __('Industry') }}</x-required-label></x-slot:label>
                    </x-select>

                    <x-select
                        wire:model.live="countryId"
                        :options="$this->countries"
                        option-label="label"
                        option-value="value"
                        :clearable="false"
                        min-items-for-search="1"
                        :placeholder="__('Select a country')"
                    >
                        <x-slot:label><x-required-label>{{ __('Country') }}</x-required-label></x-slot:label>
                    </x-select>

                    <x-select
                        wire:model="cityId"
                        :label="__('City')"
                        :options="$this->cities"
                        option-label="label"
                        option-value="value"
                        min-items-for-search="1"
                        :disabled="! $countryId"
                        :placeholder="$countryId ? __('Select a city') : __('Select a country first')"
                    />

                    <x-select
                        wire:model="currencyCode"
                        :label="__('Currency')"
                        :options="$this->currencies"
                        option-label="label"
                        option-value="value"
                        min-items-for-search="1"
                        :placeholder="__('Select a currency')"
                    />

                    <x-input wire:model="taxPin" type="text" :label="__('Tax PIN')" :placeholder="__('Optional — used for billing')" />

                    <x-phone-field :label="__('Phone')" wire-model="phone" code-wire-model="phoneCountryCode" />

                    <x-input wire:model="email" type="email" :label="__('Email')" placeholder="info@example.com" />

                    <div class="sm:col-span-2">
                        <x-textarea wire:model="address" :label="__('Address')" :placeholder="__('Street, building, postal address')" rows="3" />
                    </div>
                </div>

                <x-button primary type="submit" spinner="updateOrganisation">{{ __('Save') }}</x-button>
            </form>
        @endcan

        @can('delete organisation')
            <flux:separator class="my-10" />

            <div class="w-full max-w-lg space-y-5 border border-red-500 p-3 rounded-md">
                <div>
                    <flux:heading size="lg" class="text-red-600 dark:text-red-500">{{ __('Delete organisation') }}</flux:heading>
                    <flux:subheading>
                        {{ __('Permanently delete :name and all of its data — records, staff memberships and roles. This cannot be undone.', ['name' => $organisation->name]) }}
                    </flux:subheading>
                </div>

                <flux:modal.trigger name="confirm-organisation-deletion">
                    <x-button negative x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-organisation-deletion')">
                        {{ __('Delete organisation') }}
                    </x-button>
                </flux:modal.trigger>

                <flux:modal name="confirm-organisation-deletion" :show="$errors->has('password')" focusable class="max-w-lg">
                    <form wire:submit="deleteOrganisation" class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Are you sure you want to delete this organisation?') }}</flux:heading>

                            <flux:subheading>
                                {{ __('Once :name is deleted, all of its records, staff memberships and roles will be permanently removed. Please enter your password to confirm.', ['name' => $organisation->name]) }}
                            </flux:subheading>
                        </div>

                        <x-password wire:model="password" :label="__('Password')" />

                        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                            <flux:modal.close>
                                <x-button secondary>{{ __('Cancel') }}</x-button>
                            </flux:modal.close>

                            <x-button negative type="submit" spinner="deleteOrganisation">{{ __('Delete organisation') }}</x-button>
                        </div>
                    </form>
                </flux:modal>
            </div>
        @endcan
    </x-settings.layout>
</section>
