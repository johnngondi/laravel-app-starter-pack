<div>
    <flux:dropdown position="bottom" align="start" class="w-full">
        <flux:sidebar.profile
            :name="$this->current?->name ?? __('Select organisation')"
            :initials="$this->current ? \Illuminate\Support\Str::of($this->current->name)->substr(0, 2)->upper() : '—'"
            :avatar="$this->current?->profile_photo_path ? $this->current->profile_photo_url : null"
            icon:trailing="chevrons-up-down"
            data-test="organisation-switcher-button"
        />

        <flux:menu class="org-menu">
            <flux:menu.heading>{{ __('Organisations') }}</flux:menu.heading>

            @forelse ($this->organisations as $organisation)
                <flux:menu.item
                    wire:click="switch({{ $organisation->id }})"
                    :icon="$this->current?->is($organisation) ? 'check' : 'building-office'"
                    class="cursor-pointer"
                >
                    {{ $organisation->name }}
                </flux:menu.item>
            @empty
                <flux:text class="px-2 py-1.5 text-sm">{{ __('No organisations yet') }}</flux:text>
            @endforelse

            <flux:menu.separator />

            <flux:menu.item
                :href="route('organisations.create')"
                icon="plus"
                wire:navigate
            >
                {{ __('Create new organisation') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
