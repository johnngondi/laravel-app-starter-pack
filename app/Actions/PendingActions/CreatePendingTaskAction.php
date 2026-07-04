<?php

namespace App\Actions\PendingActions;

use App\Data\PendingTaskData;
use App\Models\PendingAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Creates a pending task. Works with or without an actionable resource — the
 * morph is nullable, so a task may stand alone (e.g. a dashboard nudge).
 */
class CreatePendingTaskAction
{
    public function handle(PendingTaskData $data, ?Model $actionable = null): PendingAction
    {
        $action = new PendingAction([
            'actors' => $data->actors,
            'notes' => $data->notes,
            'action_type' => $data->action_type,
            'due_at' => $data->due_at,
            'priority' => $data->priority,
            'resource_url' => $data->resource_url,
            'action_button_title' => $data->action_button_title ?: __('Open Task'),
        ]);

        if ($actionable !== null) {
            $action->actionable()->associate($actionable);
        }

        $action->save();

        return $action;
    }
}
