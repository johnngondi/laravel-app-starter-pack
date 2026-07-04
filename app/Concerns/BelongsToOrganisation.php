<?php

namespace App\Concerns;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Scopes a model to the current organisation (multi-tenancy).
 *
 * Apply this to "parent" / tenant-owned models such as Invoice, Debtor or
 * Debt — models that are directly owned by an organisation. Child models
 * (e.g. InvoiceItem) should NOT use it; they inherit tenancy through their
 * parent.
 *
 * The model's table must have a nullable-or-required `organisation_id`
 * column. When a model is created within an authenticated request, its
 * `organisation_id` is set automatically, and all queries are constrained
 * to the current organisation.
 *
 * @property int|null $organisation_id
 * @property-read Organisation|null $organisation
 */
trait BelongsToOrganisation
{
    /**
     * Boot the trait: auto-fill organisation_id and constrain queries.
     */
    public static function bootBelongsToOrganisation(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('organisation_id') === null && ($organisationId = static::currentOrganisationId()) !== null) {
                $model->setAttribute('organisation_id', $organisationId);
            }
        });

        static::addGlobalScope('organisation', function (Builder $builder): void {
            $organisationId = static::currentOrganisationId();

            if ($organisationId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('organisation_id'), $organisationId);
            }
        });
    }

    /**
     * The organisation that owns the model.
     *
     * @return BelongsTo<Organisation, $this>
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Resolve the current organisation id from the authenticated user.
     */
    protected static function currentOrganisationId(): ?int
    {
        return Auth::user()?->current_organisation_id;
    }
}
