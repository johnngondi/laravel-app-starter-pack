@php($currentOrganisation = auth()->user()?->currentOrganisation)

<div class="w-full">
    <flux:navbar>
        @if ($currentOrganisation)
            @can('update organisation')
                <flux:navbar.item
                    :href="route('organisation.settings.general', $currentOrganisation)"
                    :current="request()->routeIs('organisation.settings.general')"
                    wire:navigate
                >{{ __('General') }}</flux:navbar.item>
            @endcan

            @can('manage members')
                <flux:navbar.item
                    :href="route('organisation.settings.staff', $currentOrganisation)"
                    :current="request()->routeIs('organisation.settings.staff')"
                    wire:navigate
                >{{ __('Staff') }}</flux:navbar.item>
            @endcan

            @can('viewAny', \App\Models\Upload::class)
                <flux:navbar.item
                    :href="route('organisation.settings.uploads', $currentOrganisation)"
                    :current="request()->routeIs('organisation.settings.uploads')"
                    wire:navigate
                >{{ __('Uploads') }}</flux:navbar.item>
            @endcan
        @endif
    </flux:navbar>

    <flux:separator />

    <div class="mt-8 w-full">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>
