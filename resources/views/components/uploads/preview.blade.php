@props(['upload'])

{{-- Wide preview modal for a single upload. Public preview URL (uuid-keyed). --}}
<flux:modal name="upload-preview-{{ $upload->uuid }}" class="w-full max-w-4xl">
    <div class="space-y-4">
        <div>
            <flux:heading size="lg" class="truncate">{{ $upload->title }}</flux:heading>
            <flux:subheading>{{ \Illuminate\Support\Str::upper($upload->extension) }} · {{ $upload->humanSize() }}</flux:subheading>
        </div>

        <div class="overflow-hidden rounded-lg bg-neutral-50 dark:bg-neutral-800">
            @if ($upload->isImage())
                <img src="{{ $upload->previewUrl() }}" alt="{{ $upload->title }}" class="mx-auto max-h-[70vh] w-auto object-contain" />
            @elseif ($upload->isPdf())
                <iframe src="{{ $upload->previewUrl() }}" class="h-[70vh] w-full" title="{{ $upload->title }}"></iframe>
            @elseif ($upload->isVideo())
                <video src="{{ $upload->previewUrl() }}" controls class="max-h-[70vh] w-full"></video>
            @elseif ($upload->isAudio())
                <audio src="{{ $upload->previewUrl() }}" controls class="w-full p-6"></audio>
            @else
                <div class="flex flex-col items-center gap-3 p-12 text-center">
                    <flux:icon :name="$upload->iconName()" class="size-12 text-neutral-400" />
                    <flux:text>{{ __("This file type can't be previewed. Download it to view.") }}</flux:text>
                </div>
            @endif
        </div>

        <div class="flex justify-end gap-2">
            @can('download', $upload)
                <x-button primary icon="arrow-down-tray" :href="$upload->downloadUrl()">
                    {{ __('Download') }}
                </x-button>
            @endcan
            <flux:modal.close>
                <x-button secondary>{{ __('Close') }}</x-button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
