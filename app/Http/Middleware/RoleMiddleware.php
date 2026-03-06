<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:Admin,Manager')
     * Allows the request if the authenticated user's role matches any of the given roles.
     * Admin always has full access regardless of specified roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role?->name;

        // Admin has unrestricted access to everything
        if ($userRole === 'Admin') {
            return $next($request);
        }

        if (!empty($roles) && !in_array($userRole, $roles)) {
            Log::warning('Access denied', [
                'user_id' => $user->id,
                'role'    => $userRole,
                'url'     => $request->fullUrl(),
                'required_roles' => $roles,
            ]);
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
