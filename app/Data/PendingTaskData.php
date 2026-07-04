<?php

namespace App\Data;

use App\Concerns\InteractsWithPendingActions;
use App\Enums\Priority;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

/**
 * Describes a pending task to be created. Resolved either at the model level
 * (via {@see InteractsWithPendingActions::createPendingAction()})
 * or by an action class that creates the task for an actionable (or none).
 */
class PendingTaskData extends Data
{
    /**
     * @param  array<int, int>  $actors  Ids of the users who may act on the task.
     * @param  string  $notes  Clear notes about what needs doing.
     * @param  string  $action_type  Free-text category of the task (e.g. "review").
     * @param  CarbonInterface  $due_at  When the task is due.
     * @param  string  $resource_url  Relative link to the resource to act on.
     * @param  Priority  $priority  Task urgency.
     * @param  string|null  $action_button_title  Label for the primary action button.
     */
    public function __construct(
        public array $actors,
        public string $notes,
        public string $action_type,
        public CarbonInterface $due_at,
        public string $resource_url,
        public Priority $priority = Priority::Medium,
        public ?string $action_button_title = null,
    ) {}
}
