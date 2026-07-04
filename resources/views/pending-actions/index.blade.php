<x-layouts::app :title="__('Pending Actions')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center gap-3">
            <flux:heading size="xl">{{ __('Pending Actions') }}</flux:heading>
            @if ($pendingCount > 0)
                <flux:badge color="amber" size="sm">{{ $pendingCount }}</flux:badge>
            @endif
        </div>
        <flux:subheading>{{ __('Tasks awaiting your action.') }}</flux:subheading>

        <livewire:pending-actions.pending-actions-table />
    </div>
</x-layouts::app>
