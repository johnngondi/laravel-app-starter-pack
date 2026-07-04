{{-- Warning confirm for resolving a task. Lives inside the table component so
     its actions bind to the PowerGrid Livewire instance. --}}
<flux:modal name="confirm-pending-done" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Mark this task as done?') }}</flux:heading>
            <flux:subheading>
                {{ __('This removes the task from your pending list. This action cannot be undone.') }}
            </flux:subheading>
        </div>

        <div class="flex justify-end gap-2 rtl:space-x-reverse">
            <flux:modal.close>
                <x-button secondary>{{ __('Cancel') }}</x-button>
            </flux:modal.close>

            <x-button warning icon="check-circle" wire:click="markAsDone" spinner="markAsDone">
                {{ __('Mark as Done') }}
            </x-button>
        </div>
    </div>
</flux:modal>
