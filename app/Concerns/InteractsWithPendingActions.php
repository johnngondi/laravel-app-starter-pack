<?php

namespace App\Concerns;

use App\Actions\PendingActions\CreatePendingTaskAction;
use App\Actions\PendingActions\DeletePendingTaskAction;
use App\Data\PendingTaskData;
use App\Models\PendingAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Gives a model a polymorphic collection of pending tasks and the utilities to
 * create and remove them. Apply to any "actionable" model that can have work
 * outstanding against it (e.g. a Debt, Case or Note).
 *
 * @phpstan-require-extends Model
 */
trait InteractsWithPendingActions
{
    /**
     * The pending tasks raised against this model.
     *
     * @return MorphMany<PendingAction, $this>
     */
    public function pendingActions(): MorphMany
    {
        return $this->morphMany(PendingAction::class, 'actionable');
    }

    /**
     * Create a pending task for this model. The actionable resolves to $this.
     */
    public function createPendingAction(PendingTaskData $data): PendingAction
    {
        return app(CreatePendingTaskAction::class)->handle($data, $this);
    }

    /**
     * Remove a single pending task from this model.
     */
    public function deletePendingAction(PendingAction $pendingAction): void
    {
        app(DeletePendingTaskAction::class)->handle($pendingAction);
    }

    /**
     * Remove every pending task raised against this model.
     */
    public function clearPendingActions(): void
    {
        $this->pendingActions()
            ->get()
            ->each(fn (PendingAction $action) => $this->deletePendingAction($action));
    }
}
