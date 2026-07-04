<?php

namespace App\Enums;

enum Status: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Inactive = 'inactive';
    case Terminated = 'terminated';
    case Cancelled = 'cancelled';
    case Available = 'available';
    case Confirmed = 'confirmed';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Active, self::Confirmed,
            self::Available => 'success',
            self::Suspended => 'warning',
            self::Expired, self::Cancelled, self::Inactive, self::Terminated => 'danger',
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
