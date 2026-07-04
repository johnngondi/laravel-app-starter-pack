@props(['upload'])

{{-- Delete confirmation. The confirm button calls the host Livewire page's
     deleteUpload() action, so the WireUI `spinner` rides its wire:loading. --}}
<flux:modal name="upload-delete-{{ $upload->uuid }}" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Delete file?') }}</flux:heading>
            <flux:subheading>
                {{ __('":title" will be permanently removed. This action cannot be undone.', ['title' => $upload->title]) }}
            </flux:subheading>
        </div>

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <x-button secondary>{{ __('Cancel') }}</x-button>
            </flux:modal.close>

            <x-button
                negative
                icon="trash"
                wire:click="deleteUpload('{{ $upload->uuid }}')"
                spinner="deleteUpload('{{ $upload->uuid }}')"
            >
                {{ __('Delete') }}
            </x-button>
        </div>
    </div>
</flux:modal>
