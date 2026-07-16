<?php

namespace App\Listeners;

use App\Events\MeetingCreated;
use App\Events\MeetingUpdated;
use App\Events\MeetingCancelled;
use App\Events\MeetingRescheduled;
use App\Events\MeetingInvitationSent;
use App\Events\MeetingAccepted;
use App\Events\MeetingDeclined;
use Illuminate\Support\Facades\Log;

class LogMeetingActivityListener
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $action = match (true) {
            $event instanceof MeetingCreated => 'created',
            $event instanceof MeetingUpdated => 'updated',
            $event instanceof MeetingCancelled => 'cancelled',
            $event instanceof MeetingRescheduled => 'rescheduled',
            $event instanceof MeetingInvitationSent => 'invitation_sent',
            $event instanceof MeetingAccepted => 'accepted',
            $event instanceof MeetingDeclined => 'declined',
            default => 'unknown',
        };

        $userId = $event->user->id ?? ($event->inviter->id ?? null);
        $meetingId = $event->meeting->id;
        $meetingTitle = $event->meeting->title;

        // Log activity (future: write to activity_logs table)
        Log::info("Meeting Activity: [{$action}] Meeting #{$meetingId} '{$meetingTitle}' by User #{$userId}", [
            'action' => $action,
            'meeting_id' => $meetingId,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
