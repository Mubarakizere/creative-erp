<?php

namespace Tests\Feature\Metrics;

use App\Services\Metrics\ChartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_service_returns_correct_datasets()
    {
        $chartService = app(ChartService::class);
        $data = $chartService->getChartData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('tasksByStatus', $data);
        $this->assertArrayHasKey('tasksByPriority', $data);
        $this->assertArrayHasKey('projectProgress', $data);
        $this->assertArrayHasKey('tasksPerProject', $data);
        $this->assertArrayHasKey('monthlyTaskCompletion', $data);
        $this->assertArrayHasKey('commentsPerModule', $data);
        $this->assertArrayHasKey('commentsPerUser', $data);
        $this->assertArrayHasKey('dailyDiscussions', $data);
        $this->assertArrayHasKey('monthlyDiscussions', $data);
        $this->assertArrayHasKey('mentionsPerMonth', $data);
        $this->assertArrayHasKey('meetingsPerMonth', $data);
        $this->assertArrayHasKey('meetingsByType', $data);
        $this->assertArrayHasKey('attendanceRate', $data);
    }
}
