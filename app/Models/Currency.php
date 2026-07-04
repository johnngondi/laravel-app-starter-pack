<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $code
 * @property string $name
 * @property string|null $symbol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code', 'name', 'symbol'])]
class Currency extends Model
{
    /**
     * The ISO 4217 code is the primary key, so it is a non-incrementing string.
     */
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'code';

    /**
     * Resolve route-model bindings using the currency code.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
