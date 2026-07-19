<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Announcement;

class AnnouncementMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        // For standard dashboard stats, maybe we don't need announcement metrics as numbers,
        // but let's provide active announcements count just in case.
        return [
            'active_announcements' => $this->applyFilters(Announcement::query(), $filters)->published()->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        return [];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
