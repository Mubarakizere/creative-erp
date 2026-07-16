<?php

namespace App\Calendar;

use App\Contracts\CalendarEventProvider;
use App\DataTransferObjects\CalendarEvent;
use App\Models\Meeting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MeetingCalendarProvider implements CalendarEventProvider
{
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection
    {
        $query = Meeting::with(['project', 'creator'])
            ->betweenDates($start, $end)
            ->where('status', '!=', Meeting::STATUS_CANCELLED);

        if ($userId) {
            $query->forUser($userId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function (Meeting $meeting) {
            return new CalendarEvent(
                id: 'meeting-' . $meeting->id,
                title: $meeting->title,
                start: $meeting->start_at,
                end: $meeting->end_at,
                type: 'meeting',
                color: $this->getColorForType($meeting->meeting_type),
                url: route('admin.meetings.show', $meeting),
                allDay: false,
                meta: [
                    'meeting_type' => $meeting->meeting_type,
                    'status' => $meeting->status,
                    'location' => $meeting->location,
                    'meeting_link' => $meeting->meeting_link,
                    'project' => $meeting->project?->name,
                    'organizer' => $meeting->creator?->full_name,
                ],
            );
        });
    }

    public function getType(): string
    {
        return 'meeting';
    }

    public function getColor(): string
    {
        return '#3B82F6'; // Blue
    }

    /**
     * Get color based on meeting type.
     */
    private function getColorForType(string $type): string
    {
        return match ($type) {
            Meeting::TYPE_INTERNAL => '#3B82F6',    // Blue
            Meeting::TYPE_CLIENT => '#8B5CF6',      // Purple
            Meeting::TYPE_PROJECT => '#06B6D4',     // Cyan
            Meeting::TYPE_HR => '#F59E0B',          // Amber
            Meeting::TYPE_TRAINING => '#10B981',    // Emerald
            Meeting::TYPE_SALES => '#EF4444',       // Red
            default => '#6B7280',                   // Gray
        };
    }
}
