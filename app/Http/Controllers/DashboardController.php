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
        // Demo data — replace with real debt-collection metrics once the
        // domain models (debtors, debts, payments) are in place.
        $stats = [
            [
                'label' => __('Active Debtors'),
                'value' => '1,284',
                'change' => '+4.6%',
            ],
            [
                'label' => __('Outstanding Balance'),
                'value' => 'KSh 48.2M',
                'change' => '-1.2%',
            ],
            [
                'label' => __('Collected (This Month)'),
                'value' => 'KSh 6.9M',
                'change' => '+12.3%',
            ],
        ];

        $recentActivity = [
            ['debtor' => 'Acme Holdings', 'action' => 'Promise to pay', 'amount' => 'KSh 120,000', 'agent' => 'J. Ngondi', 'when' => '2 hours ago'],
            ['debtor' => 'Brightline Ltd', 'action' => 'Payment received', 'amount' => 'KSh 85,500', 'agent' => 'M. Achieng', 'when' => '5 hours ago'],
            ['debtor' => 'Delta Traders', 'action' => 'Case escalated', 'amount' => 'KSh 410,000', 'agent' => 'P. Kamau', 'when' => 'Yesterday'],
            ['debtor' => 'Northwind Co.', 'action' => 'Call logged', 'amount' => 'KSh 32,000', 'agent' => 'J. Ngondi', 'when' => 'Yesterday'],
        ];

        return view('dashboard', [
            'organisation' => $organisation,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
