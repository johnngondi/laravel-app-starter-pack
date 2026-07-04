<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganisation
{
    /**
     * Scope spatie/laravel-permission to the authenticated user's current organisation.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->current_organisation_id !== null) {
            setPermissionsTeamId($user->current_organisation_id);
        }

        return $next($request);
    }
}
