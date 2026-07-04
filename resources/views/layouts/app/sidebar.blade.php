<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="app-body min-h-screen bg-white dark:bg-navy-950">
        @php($currentOrganisation = auth()->user()?->currentOrganisation)

        <flux:sidebar sticky collapsible="mobile" class="aliensoft-sidebar border-e border-navy-800 bg-navy-900 dark:border-navy-800/60 dark:bg-navy-950">
            <flux:sidebar.header>
                <a href="{{ $currentOrganisation ? route('organisation.dashboard', $currentOrganisation) : route('dashboard') }}" wire:navigate class="flex flex-1 items-center justify-center px-1 py-1">
                    {{-- White wordmark, for the dark navy sidebar background --}}
                    <img src="{{ asset('AlienSoftLogo/aliensoft-horizontal.svg') }}" alt="{{ config('app.name') }}" class="h-14 w-auto" />
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <div class="px-2 pb-2 pt-1">
                <x-button primary icon="plus" href="#" class="w-full justify-center">
                    {{ __('New Case') }}
                </x-button>
            </div>

            @if ($currentOrganisation)
                <flux:sidebar.nav>
                    <flux:sidebar.item icon="squares-2x2" :href="route('organisation.dashboard', $currentOrganisation)" :current="request()->routeIs('organisation.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @php($pendingActionsCount = auth()->user()?->pendingActionsCount())
                    <flux:sidebar.item
                        icon="chart-bar"
                        :href="route('organisation.pending-actions.index', $currentOrganisation)"
                        :current="request()->routeIs('organisation.pending-actions.*')"
                        :badge="$pendingActionsCount ?: null"
                        wire:navigate
                    >
                        {{ __('Pending Actions') }}
                    </flux:sidebar.item>
                </flux:sidebar.nav>
            @endif

            <flux:spacer />

            @if ($currentOrganisation)
                <flux:sidebar.nav>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('organisation.settings.general', $currentOrganisation)" :current="request()->routeIs('organisation.settings.*')" wire:navigate>
                        {{ __('Settings') }}
                    </flux:sidebar.item>
                </flux:sidebar.nav>
            @endif

            <x-desktop-user-menu class="hidden lg:block" />
        </flux:sidebar>

        <!-- Desktop Top Bar -->
        <x-app-topbar />

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    :avatar="auth()->user()->profile_photo_path ? auth()->user()->profile_photo_url : null"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    {{-- :initials="auth()->user()->initials()" --}}
                                    :src="auth()->user()->profile_photo_url"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="user-circle" wire:navigate>
                            {{ __('Profile') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
