<?php

namespace Tests\Feature\Metrics;

use App\Services\Metrics\ReportMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_metrics_returns_correct_datasets()
    {
        $reportMetrics = app(ReportMetrics::class);
        $data = $reportMetrics->getReportSummaries();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('project_summary', $data);
        $this->assertArrayHasKey('task_summary', $data);
        $this->assertArrayHasKey('meeting_summary', $data);
        $this->assertArrayHasKey('time_summary', $data);
        $this->assertArrayHasKey('client_summary', $data);
        $this->assertArrayHasKey('organization_summary', $data);
    }
}
