<?php

namespace App\Actions\PendingActions;

use App\Models\PendingAction;

/**
 * Deletes (soft) a pending task once it has been acted on or is no longer
 * relevant. Independent of any actionable resource.
 */
class DeletePendingTaskAction
{
    public function handle(PendingAction $pendingAction): void
    {
        $pendingAction->delete();
    }
}
