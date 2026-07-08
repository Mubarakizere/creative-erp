<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authService
    ) {}

    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($this->authService->attemptLogin($credentials, $remember, $request)) {
            RateLimiter::clear($request->throttleKey());

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($request->throttleKey());

        $message = $this->authService->getStatusMessage($request->email);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => $message]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }
}
