<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * Verify that the authenticated user has an 'active' status.
     * If the user is not active, log them out and redirect to login with an error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->isActive()) {
            $status = Auth::user()->status;

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match ($status) {
                'inactive' => 'Your account has been deactivated. Please contact the administrator.',
                'suspended' => 'Your account has been suspended. Please contact the administrator.',
                'locked' => 'Your account has been locked. Please contact the administrator.',
                'pending' => 'Your account is pending verification.',
                default => 'Your account is not active.',
            };

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
