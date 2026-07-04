{{-- Desktop top bar: global search, dashboard actions, notifications, language and theme switchers. --}}
<flux:header class="app-topbar max-lg:hidden border-b">
    <div class="org-switcher me-3 w-56 shrink-0">
        <livewire:organisations.organisation-switcher />
    </div>

    <x-input
        icon="magnifying-glass"
        :placeholder="__('Search...')"
        class="w-full max-w-md"
    />

    <flux:spacer />

    <x-button variant="flat" secondary size="sm" right-icon="adjustments-horizontal" href="#">
        {{ __('Customize dashboard') }}
    </x-button>

    <flux:tooltip :content="__('Notifications')" position="bottom">
        <x-button variant="flat" secondary size="sm" icon="bell" href="#" aria-label="{{ __('Notifications') }}" />
    </flux:tooltip>

    <x-language-switcher />

    <x-theme-switcher />
</flux:header>
