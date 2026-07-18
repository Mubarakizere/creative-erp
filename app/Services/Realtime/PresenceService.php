<?php

namespace App\Services\Realtime;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PresenceService
{
    /**
     * Determine if presence tracking is enabled.
     */
    public function isEnabled(): bool
    {
        return config('realtime.features.presence', false);
    }

    /**
     * Mark a user as online (heartbeat).
     */
    public function heartbeat(User $user): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $ttl = config('realtime.presence.offline_timeout', 5) * 60;
        
        Cache::put("user.{$user->id}.online", true, $ttl);
        Cache::put("user.{$user->id}.last_seen", now(), $ttl);
        
        // Track by company for active sessions count
        if ($user->company_id) {
            $companyUsers = Cache::get("company.{$user->company_id}.online_users", []);
            $companyUsers[$user->id] = now()->timestamp;
            Cache::put("company.{$user->company_id}.online_users", $companyUsers, $ttl);
        }
    }

    /**
     * Check if a user is currently online.
     */
    public function isOnline(User $user): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        return Cache::has("user.{$user->id}.online");
    }

    /**
     * Get the active session count for a company.
     */
    public function getActiveSessionCount(?int $companyId = null): int
    {
        if (!$this->isEnabled() || !$companyId) {
            return 0;
        }

        $companyUsers = Cache::get("company.{$companyId}.online_users", []);
        
        // Clean up expired sessions
        $timeout = now()->subMinutes(config('realtime.presence.offline_timeout', 5))->timestamp;
        
        $activeUsers = array_filter($companyUsers, function ($timestamp) use ($timeout) {
            return $timestamp >= $timeout;
        });
        
        return count($activeUsers);
    }
}
