<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $latitude
 * @property string|null $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Country $country
 */
#[Fillable(['country_id', 'name', 'latitude', 'longitude'])]
class City extends Model
{
    /**
     * The country the city belongs to.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
