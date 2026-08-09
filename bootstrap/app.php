<?php

use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrackLastActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'check.status' => CheckUserStatus::class,
            'track.activity' => TrackLastActivity::class,
            'ensure.role' => EnsureUserHasRole::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF Token Expiration (Session timeout) gracefully
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please log in again.'], 419);
            }
            return redirect()->back()->withInput($request->except(['password', '_token']))->with('error', 'Your session has expired. Please try again.');
        });

        // Handle File Upload Too Large
        $exceptions->renderable(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The uploaded file is too large. Please upload a smaller file.'], 413);
            }
            return redirect()->back()->withInput()->with('error', 'The uploaded file exceeds the maximum allowed size.');
        });
        
        // Handle generic Symfony HTTP exceptions (they will automatically use our custom views)
    })->create();
