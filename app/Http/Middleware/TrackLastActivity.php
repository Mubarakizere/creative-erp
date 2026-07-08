<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActivity
{
    /**
     * Handle an incoming request.
     *
     * Update the authenticated user's last_activity timestamp.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Auth::user()->update([
                'last_activity' => now(),
            ]);
        }

        return $next($request);
    }
}
