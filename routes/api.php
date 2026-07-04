<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrganisationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Issue an API token from credentials (public).
    Route::post('tokens', [AuthController::class, 'store'])->name('api.tokens.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('tokens', [AuthController::class, 'destroy'])->name('api.tokens.destroy');

        Route::get('user', fn (Request $request) => $request->user())->name('api.user');

        Route::get('organisations', [OrganisationController::class, 'index'])->name('api.organisations.index');
    });
});
