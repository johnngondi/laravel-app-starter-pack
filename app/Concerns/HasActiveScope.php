<?php

namespace App\Concerns;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;

trait HasActiveScope
{
    public function scopeIsActive(Builder $builder, string $statusField = 'status'): void
    {
        $builder->where($statusField, Status::Active);
    }
}
