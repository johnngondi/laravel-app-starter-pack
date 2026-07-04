<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div>
            <flux:heading size="xl">{{ $organisation->name }}</flux:heading>
            <flux:subheading>{{ __('Debt collection overview') }}</flux:subheading>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @foreach ($stats as $stat)
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                    <flux:subheading>{{ $stat['label'] }}</flux:subheading>
                    <div class="mt-2 flex items-baseline gap-2">
                        <flux:heading size="xl">{{ $stat['value'] }}</flux:heading>
                        <flux:badge size="sm" :color="str_starts_with($stat['change'], '-') ? 'red' : 'green'">
                            {{ $stat['change'] }}
                        </flux:badge>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <flux:heading size="lg">{{ __('Recent activity') }}</flux:heading>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-neutral-200 text-xs uppercase tracking-wide text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                            <th class="py-2 pe-4 font-medium">{{ __('Debtor') }}</th>
                            <th class="py-2 pe-4 font-medium">{{ __('Action') }}</th>
                            <th class="py-2 pe-4 font-medium">{{ __('Amount') }}</th>
                            <th class="py-2 pe-4 font-medium">{{ __('Agent') }}</th>
                            <th class="py-2 font-medium">{{ __('When') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentActivity as $row)
                            <tr class="border-b border-neutral-100 last:border-0 dark:border-neutral-800">
                                <td class="py-2 pe-4 font-medium text-neutral-900 dark:text-neutral-100">{{ $row['debtor'] }}</td>
                                <td class="py-2 pe-4 text-neutral-600 dark:text-neutral-300">{{ $row['action'] }}</td>
                                <td class="py-2 pe-4 text-neutral-600 dark:text-neutral-300">{{ $row['amount'] }}</td>
                                <td class="py-2 pe-4 text-neutral-600 dark:text-neutral-300">{{ $row['agent'] }}</td>
                                <td class="py-2 text-neutral-600 dark:text-neutral-300">{{ $row['when'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>
