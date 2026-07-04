@props([
    'label' => null,
    // Livewire mode: pass `wire-model`. Plain-form mode: pass `name`.
    'wireModel' => null,
    'name' => null,
    // Current/initial image URL (null shows a placeholder).
    'previewUrl' => null,
    // Whether a photo currently exists (controls the Remove button in edit flows).
    'hasPhoto' => false,
    // Livewire action to call to remove the current photo (edit flows only).
    'removeAction' => null,
    'accept' => 'image/png,image/jpeg,image/webp',
    'shape' => 'circle',
])

@php
    $label ??= __('Photo');
    $errorKey = $wireModel ?: $name;
    $rounded = $shape === 'circle' ? 'rounded-full' : 'rounded-xl';
@endphp

<div {{ $attributes->only('class') }} x-data="{ preview: @js($previewUrl), hasPhoto: @js((bool) $hasPhoto) }">
    @if ($label)
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
    @endif

    <div class="flex items-center gap-4">
        <span class="relative grid size-16 shrink-0 place-items-center overflow-hidden {{ $rounded }} bg-neutral-100 ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700">
            <img x-show="preview" :src="preview" alt="{{ $label }}" class="absolute inset-0 h-full w-full object-cover" />
            <flux:icon name="photo" x-show="! preview" x-cloak class="size-6 text-neutral-400" />
        </span>

        <div class="flex flex-col items-start gap-1">
            <div class="flex items-center gap-2">
                <label class="inline-flex cursor-pointer items-center rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-sm font-medium text-neutral-700 shadow-sm hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700">
                    {{ __('Choose image') }}
                    <input
                        type="file"
                        class="sr-only"
                        accept="{{ $accept }}"
                        @if ($wireModel) wire:model="{{ $wireModel }}" @else name="{{ $name }}" @endif
                        x-on:change="const file = $event.target.files[0]; if (file) { preview = URL.createObjectURL(file); hasPhoto = true; }"
                    />
                </label>

                @if ($removeAction)
                    <flux:button
                        type="button"
                        variant="subtle"
                        size="sm"
                        x-show="hasPhoto"
                        x-cloak
                        wire:click="{{ $removeAction }}"
                        x-on:click="preview = null; hasPhoto = false;"
                    >
                        {{ __('Remove') }}
                    </flux:button>
                @endif
            </div>

            <flux:text class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('PNG, JPG or WEBP, up to 2MB.') }}</flux:text>

            @error($errorKey)
                <flux:text class="text-xs text-red-600 dark:text-red-500">{{ $message }}</flux:text>
            @enderror
        </div>
    </div>
</div>
