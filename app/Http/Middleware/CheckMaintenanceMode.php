<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settingsService = app(SettingsService::class);
        $isMaintenance = $settingsService->get('maintenance_mode', false);

        if (!$isMaintenance) {
            return $next($request);
        }

        // Allow routes that shouldn't be blocked
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        // Allow super admins to bypass
        if (Auth::check() && Auth::user()->hasRole('Super Admin')) {
            return $next($request);
        }

        return response()->view('errors.503', [], 503);
    }

    /**
     * Determine if the request has a URI that should pass through maintenance mode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request): bool
    {
        $except = [
            'login',
            'logout',
            'password/*',
            'api/health',
        ];

        foreach ($except as $exceptUrl) {
            if ($exceptUrl !== '/') {
                $exceptUrl = trim($exceptUrl, '/');
            }

            if ($request->fullUrlIs($exceptUrl) || $request->is($exceptUrl)) {
                return true;
            }
        }

        return false;
    }
}
