<div class="flex flex-col gap-6">
    <div class="text-center">
        <flux:heading size="xl">{{ __('Create your organisation') }}</flux:heading>
        <flux:subheading>
            {{ __('Everything in AlienSoft App belongs to an organisation. Set yours up to get started.') }}
        </flux:subheading>
    </div>

    {{-- Step indicator --}}
    <ol class="flex items-center justify-center gap-3">
        @foreach ($this->steps as $index => $label)
            @php($number = $index + 1)
            <li class="flex items-center gap-2">
                <span @class([
                    'flex size-7 items-center justify-center rounded-full text-sm font-semibold',
                    'bg-primary-600 text-white' => $step >= $number,
                    'bg-neutral-200 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400' => $step < $number,
                ])>
                    {{ $number }}
                </span>
                <span @class([
                    'text-sm',
                    'font-medium text-neutral-900 dark:text-neutral-100' => $step >= $number,
                    'text-neutral-500 dark:text-neutral-400' => $step < $number,
                ])>
                    {{ $label }}
                </span>
            </li>

            @unless ($loop->last)
                <li class="h-px w-8 bg-neutral-200 dark:bg-neutral-700" aria-hidden="true"></li>
            @endunless
        @endforeach
    </ol>

    <form wire:submit="submit" class="flex flex-col gap-6 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
        {{-- Step 1: Organisation details --}}
        <div @class(['grid grid-cols-1 gap-4 sm:grid-cols-2', 'hidden' => $step !== 1])>
            <x-avatar-upload
                class="sm:col-span-2"
                :label="__('Logo')"
                wire-model="logo"
                shape="rounded"
            />

            <x-input wire:model="name" type="text" :placeholder="__('e.g. AlienSoft Ltd')">
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
        </div>

        {{-- Step 2: Contact details --}}
        @if ($step === 2)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-phone-field :label="__('Phone')" wire-model="phone" code-wire-model="phoneCountryCode" />

                <x-input wire:model="email" type="email" :label="__('Email')" placeholder="info@example.com" />

                <div class="sm:col-span-2">
                    <x-textarea wire:model="address" :label="__('Address')" :placeholder="__('Street, building, postal address')" rows="3" />
                </div>
            </div>
        @endif

        {{-- Step 3: Review & create --}}
        @if ($step === 3)
            <div class="flex flex-col gap-4">
                <flux:heading size="lg">{{ __('Review') }}</flux:heading>

                <dl class="grid grid-cols-1 gap-x-6 sm:grid-cols-2">
                    @foreach ($this->review as $label => $value)
                        <div @class([
                            'flex items-center justify-between gap-4 border-b border-neutral-200 py-3 dark:border-neutral-700',
                            'sm:col-span-2' => $label === __('Address'),
                        ])>
                            <dt class="text-sm text-neutral-500 dark:text-neutral-400">{{ $label }}</dt>
                            <dd class="text-end text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                <flux:text class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('You can change these details later from the organisation settings.') }}
                </flux:text>
            </div>
        @endif

        {{-- Navigation --}}
        <div class="flex items-center justify-between gap-3">
            @if ($step > 1)
                <x-button type="button" secondary wire:click="previousStep">
                    {{ __('Back') }}
                </x-button>
            @else
                <span></span>
            @endif

            @if ($step < count($this->steps))
                <x-button type="submit" primary>
                    {{ __('Continue') }}
                </x-button>
            @else
                <x-button type="submit" primary spinner="submit">
                    {{ __('Create organisation') }}
                </x-button>
            @endif
        </div>
    </form>
</div>
