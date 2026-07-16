<?php

namespace App\Services;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    /**
     * Get time statistics for dashboard cards
     */
    public function getDashboardTimeStats($companyId)
    {
        $today = now()->startOfDay();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        $baseQuery = TimeEntry::where('company_id', $companyId)
            ->where('status', 'completed');

        $hoursToday = (clone $baseQuery)
            ->where('start_time', '>=', $today)
            ->sum('duration_minutes') / 60;

        $hoursThisWeek = (clone $baseQuery)
            ->where('start_time', '>=', $startOfWeek)
            ->sum('duration_minutes') / 60;

        $billableHours = (clone $baseQuery)
            ->where('start_time', '>=', $startOfMonth)
            ->where('billable', true)
            ->sum('duration_minutes') / 60;

        $nonBillableHours = (clone $baseQuery)
            ->where('start_time', '>=', $startOfMonth)
            ->where('billable', false)
            ->sum('duration_minutes') / 60;

        $runningTimersCount = TimeEntry::where('company_id', $companyId)
            ->where('status', 'running')
            ->count();

        $activeUsersCount = TimeEntry::where('company_id', $companyId)
            ->where('status', 'running')
            ->distinct('user_id')
            ->count();

        return [
            'hours_today' => round($hoursToday, 2),
            'hours_this_week' => round($hoursThisWeek, 2),
            'billable_hours_month' => round($billableHours, 2),
            'non_billable_hours_month' => round($nonBillableHours, 2),
            'running_timers_count' => $runningTimersCount,
            'users_tracking_time' => $activeUsersCount,
        ];
    }
}
