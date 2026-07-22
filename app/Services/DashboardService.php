<?php

namespace App\Services;

use App\Services\Metrics\MetricsService;

class DashboardService
{
    /**
     * @var MetricsService
     */
    protected $metricsService;

    /**
     * DashboardService constructor.
     * 
     * @param MetricsService $metricsService
     */
    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get all dashboard statistics and data.
     * This acts as a backward compatibility wrapper around MetricsService.
     *
     * @return array
     */
    public function getDashboardData(array $filters = []): array
    {
        return $this->metricsService->dashboard($filters);
    }
}
