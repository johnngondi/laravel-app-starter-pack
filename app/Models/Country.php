<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $native
 * @property string $code
 * @property string $phone_code
 * @property string|null $continent
 * @property string|null $capital
 * @property string|null $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, City> $cities
 */
#[Fillable(['name', 'native', 'code', 'phone_code', 'continent', 'capital', 'currency'])]
class Country extends Model
{
    /**
     * The cities that belong to the country.
     *
     * @return HasMany<City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
