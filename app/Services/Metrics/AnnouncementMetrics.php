<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Announcement;

class AnnouncementMetrics implements MetricProvider
{
    public function cards(): array
    {
        // For standard dashboard stats, maybe we don't need announcement metrics as numbers,
        // but let's provide active announcements count just in case.
        return [
            'active_announcements' => Announcement::published()->count(),
        ];
    }

    public function widgets(): array
    {
        return [];
    }

    public function reports(): array
    {
        return [];
    }
}
