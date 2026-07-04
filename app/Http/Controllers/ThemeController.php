<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    /**
     * Persist the member's preferred appearance so it resolves on the next
     * login and stays in sync across the devices they sign in from.
     */
    public function update(Request $request): Response
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['system', 'light', 'dark'])],
        ]);

        $request->user()->forceFill(['theme' => $validated['theme']])->save();

        return response()->noContent();
    }
}
