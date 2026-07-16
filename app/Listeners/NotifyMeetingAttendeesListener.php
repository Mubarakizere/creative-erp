<?php

namespace App\Listeners;

use App\Events\MeetingCreated;
use App\Events\MeetingUpdated;
use App\Events\MeetingCancelled;
use App\Events\MeetingRescheduled;
use App\Events\MeetingInvitationSent;
use Illuminate\Support\Facades\Log;

/**
 * Notification stubs for meeting attendees.
 *
 * Realtime notification implementation is deferred per Sprint 14 requirements.
 * This listener prepares the notification dispatch architecture for future sprints.
 */
class NotifyMeetingAttendeesListener
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Future: dispatch actual notifications (email, in-app, SMS, push)
        // For now, log the notification intent

        if ($event instanceof MeetingInvitationSent) {
            Log::info("Notification: Meeting invitation for '{$event->meeting->title}' sent to {$event->invitee->full_name}");
            return;
        }

        if ($event instanceof MeetingCancelled || $event instanceof MeetingRescheduled) {
            $action = $event instanceof MeetingCancelled ? 'cancelled' : 'rescheduled';
            Log::info("Notification: Meeting '{$event->meeting->title}' has been {$action}. Notifying all attendees.");
            return;
        }

        if ($event instanceof MeetingUpdated) {
            Log::info("Notification: Meeting '{$event->meeting->title}' has been updated.");
            return;
        }
    }
}
