<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FR-0.3 — Role-based access control middleware.
 *
 * Usage in routes:
 *   ->middleware('role:staff')   — requires staff or admin
 *   ->middleware('role:admin')   — requires admin only
 *
 * The role hierarchy is: admin > staff > renter.
 */
class EnsureUserHasRole
{
    private const HIERARCHY = [
        'renter' => 3,
        'staff'  => 2,
        'admin'  => 1,
    ];

    public function handle(Request $request, Closure $next, string $minimumRole): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $userLevel    = self::HIERARCHY[$user->role] ?? 0;
        $requiredLevel = self::HIERARCHY[$minimumRole] ?? 0;

        if ($userLevel < $requiredLevel) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
