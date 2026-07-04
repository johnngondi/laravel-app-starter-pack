@props([
    'model' => null,
    'name' => 'upload-create',
])

{{-- Create modal: multipart form -> POST /uploads. Reopens itself on validation
     failure via :show so the errors stay visible. --}}
<flux:modal name="{{ $name }}" :show="$errors->hasAny(['file', 'title', 'description'])" class="max-w-lg">
    <form method="POST" action="{{ route('uploads.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($model)
            <input type="hidden" name="owner_type" value="{{ $model->getMorphClass() }}" />
            <input type="hidden" name="owner_id" value="{{ $model->getKey() }}" />
        @endif

        <div>
            <flux:heading size="lg">{{ __('Upload file') }}</flux:heading>
            <flux:subheading>{{ __('Add a file to this list.') }}</flux:subheading>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                <x-required-label>{{ __('File') }}</x-required-label>
            </label>
            <input
                type="file"
                name="file"
                required
                class="block w-full text-sm text-neutral-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border file:border-neutral-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-neutral-700 hover:file:bg-neutral-50 dark:text-neutral-300 dark:file:border-neutral-600 dark:file:bg-neutral-800 dark:file:text-neutral-200"
            />
            @error('file')
                <flux:text class="mt-1 text-xs text-red-600 dark:text-red-500">{{ $message }}</flux:text>
            @enderror
        </div>

        <flux:input name="title" :label="__('Title')" :description="__('Optional — defaults to the file name.')" />
        <flux:textarea name="description" :label="__('Description')" rows="2" />

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <x-button type="button" secondary>{{ __('Cancel') }}</x-button>
            </flux:modal.close>
            <x-submit-button icon="arrow-up-tray">{{ __('Upload') }}</x-submit-button>
        </div>
    </form>
</flux:modal>
