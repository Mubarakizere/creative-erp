<?php

namespace App\Services\Realtime;

use Illuminate\Support\Facades\Log;

class BroadcastService
{
    /**
     * Determine if broadcasting is enabled.
     */
    public function isEnabled(): bool
    {
        return config('realtime.features.broadcasting', false) && config('realtime.provider') !== 'null';
    }

    /**
     * Dispatch an event to the realtime provider.
     * Business logic modules should use this instead of native broadcast().
     */
    public function dispatch($event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            // Future queue logic can be implemented here to prevent duplicate broadcasts.
            // For now, we delegate to Laravel's native event broadcasting.
            broadcast($event);
        } catch (\Exception $e) {
            Log::error('BroadcastService dispatch failed: ' . $e->getMessage(), [
                'event' => get_class($event)
            ]);
        }
    }

    /**
     * Broadcast to specific users privately.
     */
    public function dispatchToUsers(array $userIds, $event): void
    {
        // Implementation for specific user private broadcasting
        $this->dispatch($event);
    }
}
