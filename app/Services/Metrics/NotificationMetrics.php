<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Services\NotificationService;

class NotificationMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function __construct(protected NotificationService $notificationService) {}

    public function cards(array $filters = []): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        return [
            'unread_notifications' => $this->notificationService->getUnreadCount($user),
            'critical_notifications' => $user->notifications()
                                             ->where('priority', 'Critical')
                                             ->whereNull('read_at')
                                             ->count(),
            'today_notifications' => $user->notifications()
                                          ->whereDate('created_at', today())
                                          ->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        return [
            'recentNotifications' => $this->notificationService->getRecentNotifications($user),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
