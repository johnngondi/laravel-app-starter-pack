<section class="w-full">
    <x-settings.layout
        :heading="__('Staff')"
        :subheading="__('Add people to your organisation and assign their role.')"
    >
        <livewire:organisations.staff-manager
            :organisation="$organisation"
            :key="'staff-manager-'.$organisation->getKey()"
        />
    </x-settings.layout>
</section>
