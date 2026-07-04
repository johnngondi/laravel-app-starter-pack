<section class="w-full">
    <x-settings.layout
        :heading="__('Uploads')"
        :subheading="__('Manage files belonging to your organisation.')"
    >
        <x-uploads.table
            :uploads="$organisation->uploads()->latest()->get()"
            :model="$organisation"
            :can-create="auth()->user()->can('create', \App\Models\Upload::class)"
        />
    </x-settings.layout>
</section>
