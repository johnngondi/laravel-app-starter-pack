@props([
    // Collection / array of App\Models\Upload records.
    'uploads' => [],
    // Optional owner model (must use HasUploads) new uploads are attached to.
    'model' => null,
    // Show the upload control. Still gated by the Upload create policy.
    'canCreate' => false,
])

@php
    $createModal = 'upload-create-'.($model
        ? \Illuminate\Support\Str::slug(str_replace('\\', '-', $model->getMorphClass()).'-'.$model->getKey())
        : 'standalone');
    $showCreate = $canCreate && auth()->check() && auth()->user()->can('create', \App\Models\Upload::class);

    // Lower-cased haystacks used for instant client-side filtering.
    $haystacks = collect($uploads)
        ->map(fn ($upload) => \Illuminate\Support\Str::lower(trim($upload->title.' '.$upload->extension.' '.$upload->file_name)))
        ->values();
@endphp

<div
    x-data="{
        search: '',
        haystacks: @js($haystacks),
        matches(haystack) {
            return ! this.search || haystack.includes(this.search.toLowerCase());
        },
        get hasMatches() {
            return this.haystacks.some((haystack) => this.matches(haystack));
        },
    }"
    {{ $attributes->merge(['class' => 'rounded-xl border border-neutral-200 dark:border-neutral-700']) }}
>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-200 px-4 py-3 dark:border-neutral-700">
        <div>
            <flux:heading size="sm">{{ __('Files') }}</flux:heading>
            <flux:text class="text-xs text-neutral-500">{{ trans_choice('{0}No files|{1}:count file|[2,*]:count files', count($uploads), ['count' => count($uploads)]) }}</flux:text>
        </div>

        <div class="flex items-center gap-2">
            @if (count($uploads))
                <x-input
                    type="search"
                    icon="magnifying-glass"
                    :placeholder="__('Search files')"
                    x-model="search"
                    class="w-full sm:w-56"
                />
            @endif

            @if ($showCreate)
                <x-button primary icon="arrow-up-tray" x-on:click="$flux.modal('{{ $createModal }}').show()">
                    {{ __('Upload') }}
                </x-button>
            @endif
        </div>
    </div>

    @if (count($uploads))
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-neutral-500">
                    <th class="px-4 py-2 font-medium">{{ __('Name') }}</th>
                    <th class="px-4 py-2 font-medium">{{ __('Size') }}</th>
                    <th class="px-4 py-2 font-medium">{{ __('Date') }}</th>
                    <th class="px-4 py-2"><span class="sr-only">{{ __('Actions') }}</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach ($uploads as $upload)
                    <tr
                        class="text-neutral-700 dark:text-neutral-200"
                        x-show="matches(@js(\Illuminate\Support\Str::lower(trim($upload->title.' '.$upload->extension.' '.$upload->file_name))))"
                    >
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="grid size-8 shrink-0 place-items-center rounded-md bg-neutral-100 dark:bg-neutral-800">
                                    <flux:icon :name="$upload->iconName()" class="size-4 text-neutral-500" />
                                </span>
                                <div class="min-w-0">
                                    <div class="truncate font-medium">{{ $upload->title }}</div>
                                    <div class="truncate text-xs text-neutral-500">{{ \Illuminate\Support\Str::upper($upload->extension) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-2.5 text-neutral-500">{{ $upload->humanSize() }}</td>
                        <td class="whitespace-nowrap px-4 py-2.5 text-neutral-500">{{ $upload->created_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-2.5">
                            @php
                                $canDownload = auth()->user()?->can('download', $upload);
                                $canDelete = auth()->user()?->can('delete', $upload);
                            @endphp
                            <div class="flex justify-end">
                                <div class="inline-flex items-center">
                                    {{-- Primary action: preview --}}
                                    <x-button
                                        sm
                                        outline
                                        secondary
                                        icon="eye"
                                        class="!rounded-e-none"
                                        x-on:click="$flux.modal('upload-preview-{{ $upload->uuid }}').show()"
                                    >
                                        {{ __('Preview') }}
                                    </x-button>

                                    {{-- Split: download / delete --}}
                                    @if ($canDownload || $canDelete)
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <x-button sm outline secondary icon="chevron-down" class="!rounded-s-none -ms-px" />
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
                            </div>
                        </td>
                    </tr>
                @endforeach

                {{-- Shown when a search filters every row out. --}}
                <tr x-show="search && ! hasMatches" x-cloak>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-neutral-500">
                        {{ __('No files match your search.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="flex flex-col items-center gap-2 px-4 py-10 text-center">
            <flux:icon name="document" class="size-8 text-neutral-300 dark:text-neutral-600" />
            <flux:text class="text-sm text-neutral-500">{{ __('No files yet.') }}</flux:text>
        </div>
    @endif
</div>

{{-- Per-upload modals (teleported to body by Flux). --}}
@foreach ($uploads as $upload)
    <x-uploads.preview :upload="$upload" />
    @can('delete', $upload)
        <x-uploads.delete-modal :upload="$upload" />
    @endcan
@endforeach

@if ($showCreate)
    <x-uploads.upload-modal :model="$model" :name="$createModal" />
@endif
