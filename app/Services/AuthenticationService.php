<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthenticationService
{
    /**
     * Attempt to authenticate the user.
     */
    public function attemptLogin(array $credentials, bool $remember, Request $request): bool
    {
        $user = User::where('email', $credentials['email'])->first();

        // Record failed attempt if user not found
        if (! $user) {
            return false;
        }

        // Check if user account is active
        if (! $user->isActive()) {
            $this->recordLoginHistory($user, $request, false);

            return false;
        }

        // Attempt authentication
        if (! Auth::attempt($credentials, $remember)) {
            $this->recordLoginHistory($user, $request, false);

            return false;
        }

        // Successful login
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_activity' => now(),
        ]);

        $this->recordLoginHistory($user, $request, true);

        return true;
    }

    /**
     * Get the status message for a non-active user.
     */
    public function getStatusMessage(string $email): string
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return 'These credentials do not match our records.';
        }

        return match ($user->status) {
            'inactive' => 'Your account has been deactivated. Please contact the administrator.',
            'suspended' => 'Your account has been suspended. Please contact the administrator.',
            'locked' => 'Your account has been locked due to multiple failed login attempts. Please contact the administrator.',
            'pending' => 'Your account is pending verification. Please check your email.',
            default => 'These credentials do not match our records.',
        };
    }

    /**
     * Log the user out and record the logout event.
     */
    public function logout(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            // Update the most recent login history with logout time
            LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->where('login_successful', true)
                ->latest('login_at')
                ->first()
                ?->update(['logout_at' => now()]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Send a password reset link to the given email.
     */
    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
    }

    /**
     * Record a login history entry.
     */
    public function recordLoginHistory(User $user, Request $request, bool $successful): LoginHistory
    {
        $userAgent = $request->userAgent() ?? '';

        return LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'browser' => $this->parseBrowser($userAgent),
            'platform' => $this->parsePlatform($userAgent),
            'device' => $this->parseDevice($userAgent),
            'login_successful' => $successful,
            'login_at' => now(),
        ]);
    }

    /**
     * Get active sessions for a user.
     */
    public function getActiveSessions(User $user): Collection
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                $session->last_active = \Carbon\Carbon::createFromTimestamp($session->last_activity);

                return $session;
            });
    }

    /**
     * Parse browser name from user agent string.
     */
    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg')) {
            return 'Edge';
        }
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        }
        if (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        }
        if (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            return 'Opera';
        }

        return 'Unknown';
    }

    /**
     * Parse platform from user agent string.
     */
    private function parsePlatform(string $userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        }
        if (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            return 'macOS';
        }
        if (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        }
        if (str_contains($userAgent, 'Android')) {
            return 'Android';
        }
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'iOS';
        }

        return 'Unknown';
    }

    /**
     * Parse device type from user agent string.
     */
    private function parseDevice(string $userAgent): string
    {
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android')) {
            return 'Mobile';
        }
        if (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
