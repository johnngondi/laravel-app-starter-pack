<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasAccessibleToUserScope
{
    public function scopeAccessibleToUser(Builder $builder, User $user): void
    {
        $builder->whereIn('facility_id', $user->accessible_facilities);
    }
}
