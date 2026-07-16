<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

/**
 * Placeholder for dashboard calendar cache invalidation.
 *
 * When caching is implemented for dashboard widgets,
 * this listener will clear relevant caches on meeting changes.
 */
class RefreshDashboardCalendarListener
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Future: invalidate dashboard calendar caches
        // Cache::tags(['dashboard', 'calendar'])->flush();
        Log::debug("Dashboard calendar refresh triggered for meeting: {$event->meeting->title}");
    }
}
