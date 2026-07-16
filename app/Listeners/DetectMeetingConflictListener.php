<?php

namespace App\Listeners;

use App\Events\MeetingCreated;
use App\Events\MeetingUpdated;
use App\Services\MeetingService;
use Illuminate\Support\Facades\Log;

/**
 * Detects and logs scheduling conflicts after meeting creation/update.
 */
class DetectMeetingConflictListener
{
    protected MeetingService $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if (!($event instanceof MeetingCreated) && !($event instanceof MeetingUpdated)) {
            return;
        }

        $meeting = $event->meeting;
        $attendeeIds = $meeting->attendees()->pluck('users.id')->toArray();

        if (empty($attendeeIds)) {
            return;
        }

        $conflicts = $this->meetingService->detectConflictsForAttendees(
            $meeting->start_at->toDateTimeString(),
            $meeting->end_at->toDateTimeString(),
            $attendeeIds,
            $meeting->id
        );

        if (!empty($conflicts)) {
            foreach ($conflicts as $conflict) {
                Log::warning("Meeting Conflict: {$conflict['user']->full_name} has {$conflict['conflicts']->count()} overlapping meeting(s) with '{$meeting->title}'", [
                    'meeting_id' => $meeting->id,
                    'user_id' => $conflict['user']->id,
                    'conflict_ids' => $conflict['conflicts']->pluck('id')->toArray(),
                ]);
            }
        }
    }
}
