<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\User;
use App\Events\MeetingCreated;
use App\Events\MeetingUpdated;
use App\Events\MeetingCancelled;
use App\Events\MeetingRescheduled;
use App\Events\MeetingInvitationSent;
use App\Events\MeetingAccepted;
use App\Events\MeetingDeclined;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

class MeetingService
{
    /**
     * Create a new meeting.
     */
    public function createMeeting(array $data, array $attendeeIds = []): Meeting
    {
        return DB::transaction(function () use ($data, $attendeeIds) {
            // Validate duration
            $this->validateDuration($data['start_at'], $data['end_at']);

            $meeting = Meeting::create($data);

            // Attach attendees if provided
            if (!empty($attendeeIds)) {
                $this->syncAttendees($meeting, $attendeeIds);
            } else {
                // If no attendees provided, just attach creator
                $meeting->attendees()->attach($data['created_by'], [
                    'attendance_status' => Meeting::ATTENDANCE_ACCEPTED,
                    'response_at' => now(),
                ]);
            }

            event(new MeetingCreated($meeting, auth()->user()));

            return $meeting->load('attendees');
        });
    }

    /**
     * Update an existing meeting.
     */
    public function updateMeeting(Meeting $meeting, array $data, ?array $attendeeIds = null): Meeting
    {
        return DB::transaction(function () use ($meeting, $data, $attendeeIds) {
            if (isset($data['start_at']) && isset($data['end_at'])) {
                $this->validateDuration($data['start_at'], $data['end_at']);
            }

            $changes = array_diff_assoc($data, $meeting->getAttributes());
            $meeting->update($data);

            if ($attendeeIds !== null) {
                $this->syncAttendees($meeting, $attendeeIds);
            }

            event(new MeetingUpdated($meeting, auth()->user(), $changes));

            return $meeting->fresh('attendees');
        });
    }

    /**
     * Delete (soft delete) a meeting.
     */
    public function deleteMeeting(Meeting $meeting): bool
    {
        return $meeting->delete();
    }

    /**
     * Restore a soft-deleted meeting.
     */
    public function restoreMeeting(Meeting $meeting): bool
    {
        return $meeting->restore();
    }

    /**
     * Cancel a meeting.
     */
    public function cancelMeeting(Meeting $meeting): Meeting
    {
        $meeting->update(['status' => Meeting::STATUS_CANCELLED]);

        event(new MeetingCancelled($meeting, auth()->user()));

        return $meeting;
    }

    /**
     * Reschedule a meeting.
     */
    public function rescheduleMeeting(Meeting $meeting, string $newStartAt, string $newEndAt): Meeting
    {
        return DB::transaction(function () use ($meeting, $newStartAt, $newEndAt) {
            $this->validateDuration($newStartAt, $newEndAt);

            $oldStart = $meeting->start_at->copy();
            $oldEnd = $meeting->end_at->copy();

            $meeting->update([
                'start_at' => $newStartAt,
                'end_at' => $newEndAt,
                'status' => Meeting::STATUS_RESCHEDULED,
            ]);

            // Reset all attendee responses to pending (except organizer)
            $meeting->attendees()
                ->wherePivot('user_id', '!=', $meeting->created_by)
                ->updateExistingPivot(
                    $meeting->attendees->pluck('id')->toArray(),
                    ['attendance_status' => Meeting::ATTENDANCE_PENDING, 'response_at' => null]
                );

            event(new MeetingRescheduled($meeting, auth()->user(), $oldStart, $oldEnd));

            return $meeting->fresh('attendees');
        });
    }

    /**
     * Invite attendees to a meeting.
     */
    public function inviteAttendees(Meeting $meeting, array $userIds): void
    {
        foreach ($userIds as $userId) {
            // Skip if already an attendee
            if ($meeting->attendees()->where('users.id', $userId)->exists()) {
                continue;
            }

            $meeting->attendees()->attach($userId, [
                'attendance_status' => Meeting::ATTENDANCE_PENDING,
            ]);

            $invitee = User::find($userId);
            if ($invitee) {
                event(new MeetingInvitationSent($meeting, $invitee, auth()->user()));
            }
        }
    }

    /**
     * Accept a meeting invitation.
     */
    public function acceptInvitation(Meeting $meeting, User $user): void
    {
        $meeting->attendees()->updateExistingPivot($user->id, [
            'attendance_status' => Meeting::ATTENDANCE_ACCEPTED,
            'response_at' => now(),
        ]);

        event(new MeetingAccepted($meeting, $user));
    }

    /**
     * Decline a meeting invitation.
     */
    public function declineInvitation(Meeting $meeting, User $user): void
    {
        $meeting->attendees()->updateExistingPivot($user->id, [
            'attendance_status' => Meeting::ATTENDANCE_DECLINED,
            'response_at' => now(),
        ]);

        event(new MeetingDeclined($meeting, $user));
    }

    /**
     * Set tentative response.
     */
    public function tentativeResponse(Meeting $meeting, User $user): void
    {
        $meeting->attendees()->updateExistingPivot($user->id, [
            'attendance_status' => Meeting::ATTENDANCE_TENTATIVE,
            'response_at' => now(),
        ]);
    }

    /**
     * Detect scheduling conflicts for a user.
     */
    public function detectConflicts(string $startAt, string $endAt, int $userId, ?int $excludeMeetingId = null): Collection
    {
        $query = Meeting::where('status', '!=', Meeting::STATUS_CANCELLED)
            ->where(function ($q) use ($startAt, $endAt) {
                // Meetings that overlap with the given time range
                $q->where(function ($q2) use ($startAt, $endAt) {
                    $q2->where('start_at', '<', $endAt)
                       ->where('end_at', '>', $startAt);
                });
            })
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhereHas('attendees', function ($q2) use ($userId) {
                      $q2->where('users.id', $userId)
                         ->where('attendance_status', '!=', Meeting::ATTENDANCE_DECLINED);
                  });
            });

        if ($excludeMeetingId) {
            $query->where('id', '!=', $excludeMeetingId);
        }

        return $query->get();
    }

    /**
     * Check if there are conflicts for all provided attendees.
     */
    public function detectConflictsForAttendees(string $startAt, string $endAt, array $userIds, ?int $excludeMeetingId = null): array
    {
        $conflicts = [];

        foreach ($userIds as $userId) {
            $userConflicts = $this->detectConflicts($startAt, $endAt, $userId, $excludeMeetingId);
            if ($userConflicts->isNotEmpty()) {
                $user = User::find($userId);
                $conflicts[] = [
                    'user' => $user,
                    'conflicts' => $userConflicts,
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Validate that end time is after start time and duration is reasonable.
     */
    private function validateDuration(string $startAt, string $endAt): void
    {
        $start = \Illuminate\Support\Carbon::parse($startAt);
        $end = \Illuminate\Support\Carbon::parse($endAt);

        if ($end->lte($start)) {
            throw new Exception('End time must be after start time.');
        }

        $durationMinutes = $start->diffInMinutes($end);

        if ($durationMinutes < 5) {
            throw new Exception('Meeting must be at least 5 minutes long.');
        }

        if ($durationMinutes > 1440) { // 24 hours
            throw new Exception('Meeting cannot be longer than 24 hours.');
        }
    }

    /**
     * Sync attendees for a meeting.
     */
    private function syncAttendees(Meeting $meeting, array $userIds): void
    {
        $syncData = [];
        foreach ($userIds as $userId) {
            // Preserve existing attendance status if already an attendee
            $existing = $meeting->attendees()->where('users.id', $userId)->first();
            $syncData[$userId] = [
                'attendance_status' => $existing ? $existing->pivot->attendance_status : Meeting::ATTENDANCE_PENDING,
                'response_at' => $existing ? $existing->pivot->response_at : null,
            ];
        }

        // Always keep the creator
        if (!isset($syncData[$meeting->created_by])) {
            $syncData[$meeting->created_by] = [
                'attendance_status' => Meeting::ATTENDANCE_ACCEPTED,
                'response_at' => now(),
            ];
        }

        $meeting->attendees()->sync($syncData);
    }
}
