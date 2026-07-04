{{-- Appearance switcher (system / light / dark) backed by Flux's $flux.appearance
     store. For signed-in members the choice is also persisted to their profile so
     it resolves on the next login (see partials/head.blade.php). --}}
@props(['align' => 'end'])

<flux:dropdown position="bottom" :align="$align" x-data="themeSwitcher">
    <x-button variant="flat" secondary size="sm" aria-label="{{ __('Theme') }}">
        <flux:icon.sun x-cloak x-show="$flux.appearance === 'light'" variant="mini" class="size-5" />
        <flux:icon.moon x-cloak x-show="$flux.appearance === 'dark'" variant="mini" class="size-5" />
        <flux:icon.computer-desktop x-cloak x-show="$flux.appearance === 'system'" variant="mini" class="size-5" />
    </x-button>

    <flux:menu>
        <flux:menu.item icon="computer-desktop" x-on:click="select('system')" x-bind:class="$flux.appearance === 'system' && 'bg-zinc-100 dark:bg-white/10'">
            {{ __('System') }}
        </flux:menu.item>
        <flux:menu.item icon="sun" x-on:click="select('light')" x-bind:class="$flux.appearance === 'light' && 'bg-zinc-100 dark:bg-white/10'">
            {{ __('Light') }}
        </flux:menu.item>
        <flux:menu.item icon="moon" x-on:click="select('dark')" x-bind:class="$flux.appearance === 'dark' && 'bg-zinc-100 dark:bg-white/10'">
            {{ __('Dark') }}
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeSwitcher', () => ({
                select(value) {
                    // Apply immediately via Flux (updates localStorage + <html> class).
                    this.$flux.appearance = value

                    @auth
                        // Persist to the profile so it survives logout / new devices.
                        fetch(@js(route('theme.update')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ theme: value }),
                        })
                    @endauth
                },
            }))
        })
    </script>
@endonce
