<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Meeting;
use App\Services\CalendarService;

class MeetingMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        $userId = auth()->id();
        $companyId = auth()->user()?->hasRole('Super Admin') ? null : auth()->user()?->company_id;
        
        $calendarService = app(CalendarService::class);

        return [
            'meetings_today' => $this->applyFilters(Meeting::query(), $filters)->today()->count(),
            'upcoming_meetings' => $this->applyFilters(Meeting::query(), $filters)->upcoming()->count(),
            'schedule_conflicts' => 0, // Calculated dynamically when needed
            'events_this_week' => $userId ? $calendarService->getWeekEvents($userId, $companyId)->count() : 0,
        ];
    }

    public function widgets(array $filters = []): array
    {
        $userId = auth()->id();
        $companyId = auth()->user()?->hasRole('Super Admin') ? null : auth()->user()?->company_id;
        
        $calendarService = app(CalendarService::class);

        return [
            'upcomingMeetings' => $this->applyFilters(Meeting::query(), $filters)->upcoming()
                ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->forUser($userId)
                ->take(5)
                ->get(),
            'todaysSchedule' => $userId ? $calendarService->getTodaysSchedule($userId, $companyId) : collect(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            // Meeting Summary data
        ];
    }
}
