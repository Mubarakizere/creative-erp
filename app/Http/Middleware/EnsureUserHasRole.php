<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Verify that the authenticated user has at least one role assigned.
     * Users without any role are redirected to the "Access Pending" page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->roles->isEmpty()) {
            // Allow access to the access-pending page itself
            if ($request->routeIs('admin.access-pending')) {
                return $next($request);
            }

            return redirect()->route('admin.access-pending');
        }

        return $next($request);
    }
}
