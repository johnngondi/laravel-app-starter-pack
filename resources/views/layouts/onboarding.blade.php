<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                <x-app-logo-wordmark class="h-12 w-auto" />
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <div class="w-full max-w-xl">
                {{ $slot }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button class="cursor-pointer" type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
