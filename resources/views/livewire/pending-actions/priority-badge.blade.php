@php
    $color = match ($row->priority) {
        \App\Enums\Priority::Critical => 'red',
        \App\Enums\Priority::High => 'amber',
        \App\Enums\Priority::Medium => 'blue',
        \App\Enums\Priority::Low => 'zinc',
    };
@endphp

<flux:badge :color="$color" size="sm">{{ __(ucfirst($row->priority->value)) }}</flux:badge>
