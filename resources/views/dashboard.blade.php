<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('Overview') }}</flux:subheading>
        </div>

        {{-- Stat cards --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                    <div class="animate-pulse space-y-3">
                        <div class="h-4 w-24 rounded bg-neutral-200 dark:bg-neutral-700"></div>
                        <div class="flex items-baseline gap-2">
                            <div class="h-8 w-32 rounded bg-neutral-200 dark:bg-neutral-700"></div>
                            <div class="h-5 w-12 rounded-full bg-neutral-200 dark:bg-neutral-700"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Graph areas --}}
        <div class="grid gap-4 md:grid-cols-2">
            @for ($i = 0; $i < 2; $i++)
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                    <div class="animate-pulse space-y-4">
                        <div class="h-5 w-40 rounded bg-neutral-200 dark:bg-neutral-700"></div>
                        <div class="h-64 rounded-lg bg-neutral-200 dark:bg-neutral-700"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Table --}}
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <div class="animate-pulse space-y-4">
                <div class="h-5 w-40 rounded bg-neutral-200 dark:bg-neutral-700"></div>
                <div class="h-9 rounded bg-neutral-200 dark:bg-neutral-700"></div>
                @for ($i = 0; $i < 5; $i++)
                    <div class="h-6 rounded bg-neutral-100 dark:bg-neutral-800"></div>
                @endfor
            </div>
        </div>
    </div>
</x-layouts::app>
