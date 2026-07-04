<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasPendingActions;
use App\Concerns\HasProfilePhoto;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $tax_pin
 * @property string|null $profile_photo_path
 * @property-read string $profile_photo_url
 * @property string|null $locale
 * @property string|null $theme
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $current_organisation_id
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Organisation> $organisations
 * @property-read Organisation|null $currentOrganisation
 * @property-read Collection<int, Organisation> $ownedOrganisations
 */
#[Fillable(['name', 'email', 'phone', 'tax_pin', 'locale', 'theme', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasPendingActions, HasProfilePhoto, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * The organisations the user belongs to.
     *
     * @return BelongsToMany<Organisation, $this>
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class)->withTimestamps();
    }

    /**
     * The organisations the user owns.
     *
     * @return HasMany<Organisation, $this>
     */
    public function ownedOrganisations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'owner_id');
    }

    /**
     * The user's currently active organisation.
     *
     * @return BelongsTo<Organisation, $this>
     */
    public function currentOrganisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'current_organisation_id');
    }

    /**
     * Determine whether the user belongs to the given organisation.
     */
    public function belongsToOrganisation(Organisation $organisation): bool
    {
        return $this->organisations()->whereKey($organisation->getKey())->exists();
    }

    /**
     * Switch the user's currently active organisation.
     */
    public function switchOrganisation(Organisation $organisation): bool
    {
        if (! $this->belongsToOrganisation($organisation)) {
            return false;
        }

        $this->forceFill([
            'current_organisation_id' => $organisation->getKey(),
        ])->save();

        setPermissionsTeamId($organisation->getKey());

        return true;
    }
}
