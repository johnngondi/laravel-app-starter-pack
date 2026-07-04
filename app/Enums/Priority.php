<?php

namespace App\Enums;

enum Priority: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function color(): string
    {
        return match ($this) {
            self::Low => 'info',
            self::Medium => 'primary',
            self::High => 'warning',
            self::Critical => 'danger',
        };
    }

    /**
     * @return array{value: string, color: string, label: string}
     */
    public function get(): array
    {
        return [
            'value' => $this->value,
            'color' => $this->color(),
            'label' => ucwords($this->value),
        ];
    }
}
