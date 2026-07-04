<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PendingActionController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\EnsureOrganisationMember;
use App\Livewire\Organisations\CreateOrganisation;
use App\Livewire\Organisations\Settings as OrganisationSettings;
use App\Livewire\Organisations\StaffSettings;
use App\Livewire\Organisations\UploadSettings;
use App\Models\Organisation;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Switch the interface language. Available to guests and members alike so the
// language can be changed from the auth screens too.
Route::get('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

// Persist the selected appearance for the signed-in member.
Route::post('theme', [ThemeController::class, 'update'])->middleware('auth')->name('theme.update');

// Public, uuid-keyed upload previewing and downloading. Intentionally outside
// the auth group — the unguessable uuid is the access token.
Route::get('uploads/{upload:uuid}/preview', [UploadController::class, 'preview'])->name('uploads.preview');
Route::get('uploads/{upload:uuid}/download', [UploadController::class, 'download'])->name('uploads.download');

// Authenticated upload management.
Route::middleware('auth')->group(function () {
    Route::post('uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::delete('uploads/{upload:uuid}', [UploadController::class, 'destroy'])->name('uploads.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Onboarding: create the first (or an additional) organisation.
    Route::livewire('organisations/create', CreateOrganisation::class)->name('organisations.create');

    // Entry point after authentication — bounce to the current organisation
    // (or onboarding). Keeps a param-free `dashboard` route for redirects.
    Route::get('dashboard', [DashboardController::class, 'current'])->name('dashboard');

    // Organisation-scoped routes, keyed by the organisation slug.
    Route::middleware(EnsureOrganisationMember::class)
        ->prefix('{organisation}')
        ->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('organisation.dashboard');

            // Pending tasks awaiting the signed-in user.
            Route::get('pending-actions', [PendingActionController::class, 'index'])
                ->name('organisation.pending-actions.index');
            Route::delete('pending-actions/{pendingAction}', [PendingActionController::class, 'destroy'])
                ->name('organisation.pending-actions.destroy');

            // Organisation settings live under the sidebar settings layout, split
            // into General and Staff pages. The bare URL keeps the old route name
            // as a redirect to General for back-compat.
            Route::get('settings', fn (Organisation $organisation) => redirect()->route('organisation.settings.general', $organisation))
                ->name('organisation.settings');

            Route::livewire('settings/general', OrganisationSettings::class)->name('organisation.settings.general');

            Route::livewire('settings/staff', StaffSettings::class)->name('organisation.settings.staff');

            Route::livewire('settings/uploads', UploadSettings::class)->name('organisation.settings.uploads');
        });
});

require __DIR__.'/settings.php';
