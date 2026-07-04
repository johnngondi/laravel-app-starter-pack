<?php

namespace App\Concerns;

use App\Models\PendingAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves the pending tasks awaiting the model (a User) as an actor. A task
 * lists its eligible actors in the `actors` json column; if any one of them
 * acts, the task is removed for everyone.
 *
 * @phpstan-require-extends Model
 */
trait HasPendingActions
{
    /**
     * Query of pending tasks this user may act on.
     *
     * @return Builder<PendingAction>
     */
    public function pendingActions(): Builder
    {
        return PendingAction::query()->whereJsonContains('actors', $this->getKey());
    }

    /**
     * How many tasks are awaiting this user (for the sidebar badge).
     */
    public function pendingActionsCount(): int
    {
        return $this->pendingActions()->count();
    }
}
