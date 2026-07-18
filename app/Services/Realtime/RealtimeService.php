<?php

namespace App\Services\Realtime;

class RealtimeService
{
    public function __construct(
        protected BroadcastService $broadcastService,
        protected PresenceService $presenceService
    ) {}

    /**
     * Get the broadcast service instance.
     */
    public function broadcast(): BroadcastService
    {
        return $this->broadcastService;
    }

    /**
     * Get the presence service instance.
     */
    public function presence(): PresenceService
    {
        return $this->presenceService;
    }

    /**
     * Check if realtime architecture is globally enabled.
     */
    public function isEnabled(): bool
    {
        return config('realtime.features.enabled', false);
    }
}
