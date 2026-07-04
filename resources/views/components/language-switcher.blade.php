{{-- Interface language switcher. Locales come from config/locale.php and the
     selection is persisted to the session by the locale.update route. --}}
@props(['align' => 'end'])

@php
    $locales = config('locale.supported', []);
    $current = app()->getLocale();
    $active = $locales[$current] ?? ['short' => strtoupper($current)];
@endphp

<flux:dropdown position="bottom" :align="$align">
    <x-button variant="flat" secondary size="sm" aria-label="{{ __('Language') }}">
        <flux:icon.language variant="mini" class="size-5" />
        <span class="ms-1.5 text-xs font-semibold">{{ $active['short'] }}</span>
    </x-button>

    <flux:menu>
        @foreach ($locales as $code => $locale)
            <flux:menu.item
                href="{{ route('locale.update', $code) }}"
                icon="language"
                class="{{ $code === $current ? 'bg-zinc-100 dark:bg-white/10' : '' }}"
            >
                <span class="flex w-full items-center gap-6">
                    <span>{{ $locale['native'] }}</span>
                    <span class="ms-auto text-xs text-zinc-400">{{ $locale['short'] }}</span>
                </span>
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>
