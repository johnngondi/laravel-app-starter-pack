<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-white text-[#0B1430] antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 dark:text-white">
        <div class="flex min-h-svh flex-col">
            {{-- Top bar --}}
            <header class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-6 py-5">
                <a href="{{ route('home') }}" class="flex items-center" wire:navigate>
                    <x-app-logo-wordmark class="h-9 w-auto" />
                    <span class="sr-only">{{ config('app.name', 'AlienSoft App') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <x-language-switcher />
                    <x-theme-switcher />

                    @if (Route::has('login'))
                        @auth
                            <flux:button href="{{ route('dashboard') }}" size="sm" variant="primary" wire:navigate>
                                {{ __('Dashboard') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('login') }}" size="sm" variant="ghost" class="hidden sm:inline-flex" wire:navigate>
                                {{ __('Log in') }}
                            </flux:button>

                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" size="sm" variant="primary" wire:navigate>
                                    {{ __('Sign up') }}
                                </flux:button>
                            @endif
                        @endauth
                    @endif
                </div>
            </header>

            {{-- Hero --}}
            <main class="flex flex-1 flex-col items-center justify-center px-6 py-16 text-center">
                <h1 class="max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    {{ __('Debt recovery, powered by') }}
                    <span class="text-[#E0B53D]">{{ __('AI.') }}</span>
                </h1>

                <p class="mt-6 max-w-xl text-base leading-relaxed text-zinc-500 dark:text-zinc-400 sm:text-lg">
                    {{ __('Automate collections, prioritise high-risk accounts, and recover more — with AI-driven workflows built for modern debt collection teams.') }}
                </p>

                <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row">
                    <a
                        href="{{ Route::has('register') ? route('register') : route('home') }}"
                        @if (Route::has('register')) wire:navigate @endif
                        class="inline-flex items-center justify-center rounded-lg bg-[#E0B53D] px-7 py-3 text-sm font-semibold text-[#0B1430] shadow-sm transition hover:bg-[#cca331] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E0B53D] focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-neutral-950"
                    >
                        {{ __('Get started today') }}
                    </a>

                    <a
                        href="#demo"
                        class="group inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-semibold text-[#0B1430] transition hover:text-[#E0B53D] dark:text-white dark:hover:text-[#E0B53D]"
                    >
                        <span class="flex size-7 items-center justify-center rounded-full border border-current">
                            <svg class="ms-0.5 size-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </span>
                        {{ __('Watch a demo') }}
                    </a>
                </div>

                {{-- Partners --}}
                <div class="mt-20 w-full max-w-4xl">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                        {{ __('Trusted by these partners so far') }}
                    </p>

                    <div class="mt-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-sm font-semibold text-zinc-400 dark:text-zinc-500">
                        <span>{{ __('KCB Bank Kenya') }}</span>
                        <span>{{ __('Faulu Kenya DTM') }}</span>
                        <span>{{ __('Stanbic Microfinance') }}</span>
                        <span>{{ __('JM Realty') }}</span>
                        <span>{{ __('Unity Properties') }}</span>
                    </div>
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
