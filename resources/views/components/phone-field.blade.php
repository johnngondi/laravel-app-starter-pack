@props([
    'label' => null,
    'required' => false,
    // Plain-form mode: pass `name` / `code-name` (values come from old()).
    'name' => null,
    'codeName' => null,
    'value' => null,
    // Livewire mode: pass `wire-model` / `code-wire-model`.
    'wireModel' => null,
    'codeWireModel' => null,
    'selectedCode' => '254',
    'placeholder' => '700 000 000',
])

@php
    // The supported dialing codes, mapped to WireUI select options. Cached per
    // request so reusing the field across pages stays cheap.
    $options = once(fn () => \App\Models\Country::query()
        ->orderBy('name')
        ->get(['code', 'phone_code'])
        ->map(fn ($country) => [
            'value' => (string) $country->phone_code,
            'label' => $country->code.' +'.$country->phone_code,
        ])
        ->all());

    $label ??= __('Phone');
@endphp

<div {{ $attributes->only('class') }}>
    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
        @if ($required)
            <x-required-label>{{ $label }}</x-required-label>
        @else
            {{ $label }}
        @endif
    </label>

    {{-- The WireUI controls force `w-full`, so widths live on the wrapper divs.
         The dial-code box is a fixed width; the number takes the rest and may
         shrink (min-w-0) so it never overflows into adjacent fields. --}}
    <div class="flex items-start gap-2">
        <div class="phone-code-select w-28 shrink-0">
            @if ($codeWireModel)
                <x-select
                    :options="$options"
                    option-label="label"
                    option-value="value"
                    :clearable="false"
                    min-items-for-search="1"
                    :placeholder="__('Code')"
                    wire:model="{{ $codeWireModel }}"
                />
            @else
                <x-select
                    :options="$options"
                    option-label="label"
                    option-value="value"
                    :clearable="false"
                    min-items-for-search="1"
                    :placeholder="__('Code')"
                    name="{{ $codeName }}"
                    value="{{ old($codeName, $selectedCode) }}"
                />
            @endif
        </div>

        <div class="min-w-0 flex-1">
            @if ($wireModel)
                <x-input
                    type="tel"
                    :placeholder="$placeholder"
                    autocomplete="tel"
                    :required="$required"
                    wire:model="{{ $wireModel }}"
                />
            @else
                <x-input
                    type="tel"
                    :placeholder="$placeholder"
                    autocomplete="tel"
                    :required="$required"
                    name="{{ $name }}"
                    value="{{ old($name, $value) }}"
                />
            @endif
        </div>
    </div>
</div>
