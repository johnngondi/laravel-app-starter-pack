@props([
    'heading' => null,
    'subheading' => null,
])

<div class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
        <flux:subheading size="lg">{{ __('Manage your profile and account settings') }}</flux:subheading>
    </div>

    <div class="flex items-start max-md:flex-col">
        <div class="me-10 w-full pb-4 md:w-[220px]">
            <flux:navlist aria-label="{{ __('Account settings') }}">
                <flux:navlist.item
                    :href="route('profile.edit')"
                    :current="request()->routeIs('profile.edit')"
                    wire:navigate
                >{{ __('Profile') }}</flux:navlist.item>

                <flux:navlist.item
                    :href="route('security.edit')"
                    :current="request()->routeIs('security.edit')"
                    wire:navigate
                >{{ __('Security') }}</flux:navlist.item>
            </flux:navlist>
        </div>

        <flux:separator class="md:hidden" />

        <div class="flex-1 self-stretch max-md:pt-6">
            @if ($heading)
                <flux:heading>{{ $heading }}</flux:heading>
            @endif
            @if ($subheading)
                <flux:subheading>{{ $subheading }}</flux:subheading>
            @endif

            <div class="mt-5 w-full max-w-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
