<section class="w-full">
    {{-- Action row: the add action sits above the table, aligned to the end. --}}
    <div class="flex items-center justify-end">
        <flux:modal.trigger name="add-staff">
            <x-button primary icon="user-plus" x-data="" x-on:click="$dispatch('open-modal', 'add-staff')">
                {{ __('Add New Staff') }}
            </x-button>
        </flux:modal.trigger>
    </div>

    <flux:separator class="my-6" />

    {{-- Staff listing. --}}
    <livewire:organisations.staff-table :organisation="$organisation" :key="'staff-table-'.$organisation->getKey()" />

    {{-- Add staff modal. --}}
    <flux:modal name="add-staff" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="addStaff" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add new staff') }}</flux:heading>
                <flux:subheading>{{ __('Invite a person to your organisation and assign their role.') }}</flux:subheading>
            </div>

            <x-input wire:model="name" type="text" :placeholder="__('Full name')">
                <x-slot:label><x-required-label>{{ __('Name') }}</x-required-label></x-slot:label>
            </x-input>
            <x-input wire:model="email" type="email" placeholder="email@example.com">
                <x-slot:label><x-required-label>{{ __('Email') }}</x-required-label></x-slot:label>
            </x-input>
            <x-phone-field :label="__('Phone')" wire-model="phone" code-wire-model="phoneCountryCode" />

            {{-- Flux's native <select> so the dropdown isn't clipped by the modal. --}}
            <flux:select wire:model="role" :label="__('Role')">
                @foreach ($this->assignableRoles as $assignableRole)
                    <flux:select.option value="{{ $assignableRole }}">{{ $assignableRole }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <x-button secondary>{{ __('Cancel') }}</x-button>
                </flux:modal.close>

                <x-button primary type="submit" icon="user-plus" spinner="addStaff">{{ __('Add staff') }}</x-button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit role modal. --}}
    <flux:modal name="edit-staff-role" focusable class="max-w-lg">
        <form wire:submit="updateRole" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit role') }}</flux:heading>
                <flux:subheading>
                    {{ __('Change the role assigned to :name.', ['name' => $editingUserName]) }}
                </flux:subheading>
            </div>

            {{-- Flux's native <select> so the dropdown isn't clipped by the modal. --}}
            <flux:select wire:model="editingRole" :label="__('Role')">
                @foreach ($this->assignableRoles as $assignableRole)
                    <flux:select.option value="{{ $assignableRole }}">{{ $assignableRole }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <x-button secondary>{{ __('Cancel') }}</x-button>
                </flux:modal.close>

                <x-button primary type="submit" icon="check" spinner="updateRole">{{ __('Save role') }}</x-button>
            </div>
        </form>
    </flux:modal>

    {{-- Remove staff confirmation. --}}
    <flux:modal name="confirm-remove-staff" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Remove staff member?') }}</flux:heading>
                <flux:subheading>
                    {{ __('This removes :name from the organisation and revokes their role. This action cannot be undone.', ['name' => $removingUserName]) }}
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <x-button secondary>{{ __('Cancel') }}</x-button>
                </flux:modal.close>

                <x-button negative icon="trash" wire:click="removeStaff" spinner="removeStaff">
                    {{ __('Remove') }}
                </x-button>
            </div>
        </div>
    </flux:modal>
</section>
