<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Resolve the user's active organisation and redirect to its dashboard,
     * or to onboarding when they don't belong to one yet.
     */
    public function current(Request $request): RedirectResponse
    {
        $user = $request->user();

        $organisation = $user->currentOrganisation ?? $user->organisations()->first();

        if ($organisation === null) {
            return redirect()->route('organisations.create');
        }

        return redirect()->route('organisation.dashboard', $organisation);
    }

    /**
     * Display the dashboard for the given organisation.
     */
    public function index(Request $request, Organisation $organisation): View
    {
        return view('dashboard', [
            'organisation' => $organisation,
        ]);
    }
}
