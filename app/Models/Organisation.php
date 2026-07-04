<?php

namespace App\Models;

use App\Concerns\HasProfilePhoto;
use App\Concerns\HasUploads;
use App\Contracts\Uploadable;
use Database\Factories\OrganisationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string $slug
 * @property string|null $profile_photo_path
 * @property-read string $profile_photo_url
 * @property int|null $industry_id
 * @property int|null $country_id
 * @property int|null $city_id
 * @property string|null $currency_code
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $tax_pin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $owner
 * @property-read Collection<int, User> $members
 * @property-read Industry|null $industry
 * @property-read Country|null $country
 * @property-read City|null $city
 * @property-read Currency|null $currency
 */
#[Fillable(['owner_id', 'name', 'slug', 'industry_id', 'country_id', 'city_id', 'currency_code', 'phone', 'email', 'address', 'tax_pin'])]
class Organisation extends Model implements Uploadable
{
    /** @use HasFactory<OrganisationFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasUploads;

    /**
     * Register model event listeners.
     */
    protected static function booted(): void
    {
        // Clean up the data that isn't removed by a database cascade when an
        // organisation is deleted. The pivot (organisation_user) and other
        // tenant-owned records are handled by their cascade-on-delete foreign
        // keys; the Spatie team-scoped roles and assignments are not, so purge
        // them here.
        static::deleting(function (self $organisation): void {
            $organisation->purgePermissionData();
        });
    }

    /**
     * Resolve route-model bindings (and generate URLs) using the slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The user who owns the organisation.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The users that belong to the organisation.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Determine whether the given user is a member of the organisation.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->whereKey($user->getKey())->exists();
    }

    /**
     * The industry the organisation operates in.
     *
     * @return BelongsTo<Industry, $this>
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * The country the organisation is based in.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The city the organisation is based in.
     *
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * The default currency used for the organisation's transactions.
     *
     * @return BelongsTo<Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Create a new organisation owned by the given user, provisioning the
     * default roles and assigning the owner the "Owner" role. Additional
     * profile attributes (industry, country, currency, contact details, …)
     * may be supplied and are mass-assigned alongside the core fields.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function createForOwner(User $owner, string $name, array $attributes = []): self
    {
        $organisation = static::create([
            ...$attributes,
            'owner_id' => $owner->getKey(),
            'name' => $name,
            'slug' => static::generateUniqueSlug($name),
        ]);

        $organisation->members()->attach($owner);
        $organisation->provisionDefaultRoles();

        setPermissionsTeamId($organisation->getKey());
        $owner->assignRole('Owner');

        $owner->forceFill([
            'current_organisation_id' => $organisation->getKey(),
        ])->save();

        return $organisation;
    }

    /**
     * Create the default set of organisation-scoped roles from configuration.
     */
    public function provisionDefaultRoles(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard');

        /** @var array<string> $permissions */
        $permissions = config('organisation.permissions', []);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, $guard);
        }

        setPermissionsTeamId($this->getKey());

        /** @var array<string, array<string>> $roles */
        $roles = config('organisation.roles', []);

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, $guard);

            $role->syncPermissions(
                $rolePermissions === ['*'] ? $permissions : $rolePermissions
            );
        }
    }

    /**
     * Remove the Spatie team-scoped roles and role/permission assignments that
     * belong to this organisation. They carry the organisation id in their
     * `team_foreign_key` column but aren't foreign-key constrained, so they
     * have to be deleted explicitly. Deleting the roles cascades to the
     * `role_has_permissions` and `model_has_roles` rows that reference them.
     */
    public function purgePermissionData(): void
    {
        /** @var string $teamKey */
        $teamKey = config('permission.column_names.team_foreign_key');

        /** @var array<string, string> $tables */
        $tables = config('permission.table_names');

        DB::table($tables['model_has_roles'])->where($teamKey, $this->getKey())->delete();
        DB::table($tables['model_has_permissions'])->where($teamKey, $this->getKey())->delete();
        DB::table($tables['roles'])->where($teamKey, $this->getKey())->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Generate a unique slug for the given organisation name.
     */
    protected static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(6));
        }

        return $slug;
    }
}
