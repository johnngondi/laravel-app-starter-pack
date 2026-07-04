<?php

namespace App\Http\Controllers;

use App\Actions\PendingActions\DeletePendingTaskAction;
use App\Models\Organisation;
use App\Models\PendingAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PendingActionController extends Controller
{
    /**
     * List the pending tasks awaiting the signed-in user. The searchable,
     * paginated table is rendered by the PendingActionsTable Livewire
     * component; the count surfaces on the sidebar badge.
     */
    public function index(Request $request, Organisation $organisation): View
    {
        return view('pending-actions.index', [
            'organisation' => $organisation,
            'pendingCount' => $request->user()->pendingActionsCount(),
        ]);
    }

    /**
     * Remove a pending task. Kept as a REST endpoint; the table marks tasks
     * done in-place via Livewire. Only an eligible actor may remove a task.
     */
    public function destroy(Request $request, Organisation $organisation, PendingAction $pendingAction): RedirectResponse
    {
        abort_unless(in_array($request->user()->getKey(), $pendingAction->actors ?? [], true), 403);

        app(DeletePendingTaskAction::class)->handle($pendingAction);

        return back()->with('status', __('Task marked as done.'));
    }
}
