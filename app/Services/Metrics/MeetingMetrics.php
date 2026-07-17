<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Meeting;
use App\Services\CalendarService;

class MeetingMetrics implements MetricProvider
{
    public function cards(): array
    {
        $userId = auth()->id();
        $companyId = auth()->user()?->hasRole('Super Admin') ? null : auth()->user()?->company_id;
        
        $calendarService = app(CalendarService::class);

        return [
            'meetings_today' => Meeting::today()->count(),
            'upcoming_meetings' => Meeting::upcoming()->count(),
            'schedule_conflicts' => 0, // Calculated dynamically when needed
            'events_this_week' => $userId ? $calendarService->getWeekEvents($userId, $companyId)->count() : 0,
        ];
    }

    public function widgets(): array
    {
        $userId = auth()->id();
        $companyId = auth()->user()?->hasRole('Super Admin') ? null : auth()->user()?->company_id;
        
        $calendarService = app(CalendarService::class);

        return [
            'upcomingMeetings' => Meeting::upcoming()
                ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->forUser($userId)
                ->take(5)
                ->get(),
            'todaysSchedule' => $userId ? $calendarService->getTodaysSchedule($userId, $companyId) : collect(),
        ];
    }

    public function reports(): array
    {
        return [
            // Meeting Summary data
        ];
    }
}
