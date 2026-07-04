@props(['upload'])

@php
    $canDownload = auth()->check() && auth()->user()->can('download', $upload);
    $canDelete = auth()->check() && auth()->user()->can('delete', $upload);
@endphp

{{-- Compact single-upload card: ~50px tall, up to 300px wide. The body opens
     the preview modal; the caret splits into a download/delete menu. --}}
<div {{ $attributes->merge(['class' => 'inline-flex h-[50px] w-full max-w-[300px] items-center gap-1 rounded-lg border border-neutral-200 bg-white pe-1 dark:border-neutral-700 dark:bg-neutral-900']) }}>
    <button
        type="button"
        class="flex h-full min-w-0 flex-1 cursor-pointer items-center gap-2 rounded-s-lg px-2 text-left hover:bg-neutral-50 dark:hover:bg-neutral-800"
        x-on:click="$flux.modal('upload-preview-{{ $upload->uuid }}').show()"
    >
        <span class="grid size-8 shrink-0 place-items-center rounded-md bg-neutral-100 dark:bg-neutral-800">
            <x-icon :name="$upload->iconName()" class="size-4 text-neutral-500" />
        </span>
        <span class="min-w-0">
            <span class="block truncate text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $upload->title }}</span>
            <span class="block truncate text-xs text-neutral-500">{{ \Illuminate\Support\Str::upper($upload->extension) }} · {{ $upload->humanSize() }}</span>
        </span>
    </button>

    @if ($canDownload || $canDelete)
        <x-dropdown>
            <x-slot name="trigger">
                <x-button xs flat secondary icon="chevron-down" />
            </x-slot>

            @if ($canDownload)
                <x-dropdown.item icon="arrow-down-tray" :href="$upload->downloadUrl()">{{ __('Download') }}</x-dropdown.item>
            @endif
            @if ($canDelete)
                <x-dropdown.item
                    icon="trash"
                    class="!text-negative-600 hover:!bg-negative-50 dark:!text-negative-400 dark:hover:!bg-negative-950/40"
                    x-on:click="$flux.modal('upload-delete-{{ $upload->uuid }}').show()"
                >
                    {{ __('Delete') }}
                </x-dropdown.item>
            @endif
        </x-dropdown>
    @endif
</div>

<x-uploads.preview :upload="$upload" />
@if ($canDelete)
    <x-uploads.delete-modal :upload="$upload" />
@endif
