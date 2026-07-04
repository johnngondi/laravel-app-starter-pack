<?php

namespace App\Actions\PendingActions;

use App\Data\PendingTaskData;
use App\Enums\Priority;
use App\Models\PendingAction;
use App\Models\User;

/**
 * Test helper: raises a standalone pending task (no actionable) that nudges the
 * user to review their dashboard. Seeded via the
 * `pending-actions:seed-test` command.
 */
class TestPendingTaskAction
{
    public function __construct(private CreatePendingTaskAction $createPendingTask) {}

    public function handle(User $user): PendingAction
    {
        $organisation = $user->currentOrganisation ?? $user->organisations()->first();

        // Stored relative; rendered absolute on the UI.
        $resourceUrl = $organisation !== null
            ? route('organisation.dashboard', $organisation, absolute: false)
            : route('dashboard', absolute: false);

        $data = new PendingTaskData(
            actors: [$user->getKey()],
            notes: __('Review the movements on notes and act before end of day.'),
            action_type: 'review',
            due_at: now()->endOfDay(),
            resource_url: $resourceUrl,
            priority: Priority::High,
            action_button_title: __('Open Dashboard'),
        );

        return $this->createPendingTask->handle($data);
    }
}
