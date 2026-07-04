<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganisationMember
{
    /**
     * Ensure the authenticated user is a member of the organisation resolved
     * from the route slug, then make it the active organisation for the
     * request (current organisation + permission team scope).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $organisation = $request->route('organisation');

        if ($user === null || ! $organisation instanceof Organisation) {
            return $next($request);
        }

        if (! $user->belongsToOrganisation($organisation)) {
            abort(403);
        }

        if ($user->current_organisation_id !== $organisation->getKey()) {
            $user->forceFill([
                'current_organisation_id' => $organisation->getKey(),
            ])->save();
        }

        setPermissionsTeamId($organisation->getKey());

        return $next($request);
    }
}
