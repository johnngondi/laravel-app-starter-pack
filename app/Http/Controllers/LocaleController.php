<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Persist the selected interface locale to the session and return the
     * user to the page they switched from.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        if (array_key_exists($locale, config('locale.supported', []))) {
            $request->session()->put('locale', $locale);

            // Persist the choice to the profile so it resolves on the next login,
            // and on any other device the member signs in from.
            $request->user()?->forceFill(['locale' => $locale])->save();
        }

        return back();
    }
}
