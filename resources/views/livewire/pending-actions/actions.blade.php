@php($title = $row->action_button_title ?: __('Open Task'))

{{-- Split button: primary opens the resource, the dropdown resolves the task. --}}
<flux:button.group>
    <flux:button
        as="a"
        href="{{ url($row->resource_url) }}"
        variant="primary"
        size="sm"
        icon="arrow-up-right"
    >
        {{ $title }}
    </flux:button>

    <flux:dropdown position="bottom" align="end">
        <flux:button variant="primary" size="sm" icon="chevron-down" />

        <flux:menu>
            <flux:menu.item
                variant="danger"
                icon="check-circle"
                wire:click="confirmDone({{ $row->getKey() }})"
            >
                {{ __('Mark as Done') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:button.group>
