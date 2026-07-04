<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the locale the user selected for the current request. The session
     * holds the choice for the active session; a fresh login falls back to the
     * locale saved on the member's profile, then to the application default.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale') ?? $request->user()?->locale;

        if ($locale !== null && array_key_exists($locale, config('locale.supported', []))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
