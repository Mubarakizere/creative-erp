<?php

namespace App\Services;

use App\Models\ReportTemplate;
use App\Models\ActivityLog;
use App\Services\Metrics\MetricsService;
use App\Services\Metrics\ChartService;
use App\Services\Metrics\ReportMetrics;

class ReportService
{
    protected ReportBuilderService $builderService;
    protected MetricsService $metricsService;
    protected ChartService $chartService;
    protected ReportMetrics $reportMetrics;

    public function __construct(
        ReportBuilderService $builderService,
        MetricsService $metricsService,
        ChartService $chartService,
        ReportMetrics $reportMetrics
    ) {
        $this->builderService = $builderService;
        $this->metricsService = $metricsService;
        $this->chartService = $chartService;
        $this->reportMetrics = $reportMetrics;
    }

    /**
     * Get the data needed to render a report viewer.
     */
    public function getReportData(ReportTemplate $template, array $filters = []): array
    {
        // Merge template filters with runtime filters
        $mergedFilters = array_merge($template->filters ?? [], $filters);

        // Record activity
        $this->logActivity('report_viewed', $template);

        return [
            'template' => $template,
            'filters' => $mergedFilters,
            'kpis' => $this->getKpisForType($template->type, $mergedFilters),
            'charts' => $this->getChartsForType($template->type, $mergedFilters),
            'table_data' => $this->builderService->build($template->type, $mergedFilters),
        ];
    }

    /**
     * Uses MetricsService to get KPIs
     */
    protected function getKpisForType(string $type, array $filters): array
    {
        $summaries = $this->reportMetrics->getReportSummaries();
        
        // Return specific summary based on type. 
        // Real implementation might filter metrics based on $filters too.
        return match($type) {
            'project_summary' => $summaries['project_summary'] ?? [],
            'task_summary' => $summaries['task_summary'] ?? [],
            'time_summary' => $summaries['time_summary'] ?? [],
            'quotation_summary' => $summaries['quotation_summary'] ?? [],
            'sales_forecast' => $summaries['sales_forecast'] ?? [],
            'approval_summary' => $summaries['approval_summary'] ?? [],
            'invoice_summary' => $summaries['invoice_summary'] ?? [],
            'payment_summary' => $summaries['payment_summary'] ?? [],
            'aging_report' => $summaries['receivable_summary'] ?? [],
            'revenue_report' => $summaries['payment_summary'] ?? [],
            'customer_statements' => $summaries['receivable_summary'] ?? [],
            default => [],
        };
    }

    /**
     * Uses ChartService to get Charts
     */
    protected function getChartsForType(string $type, array $filters): array
    {
        $charts = $this->chartService->getChartData();
        
        return match($type) {
            'project_summary' => ['projectProgress' => $charts['projectProgress'] ?? []],
            'task_summary' => [
                'tasksByStatus' => $charts['tasksByStatus'] ?? [],
                'tasksByPriority' => $charts['tasksByPriority'] ?? []
            ],
            'revenue_report' => ['revenueTrends' => $charts['revenueTrends'] ?? []],
            'payment_summary' => ['paymentMethods' => $charts['paymentMethods'] ?? []],
            default => [],
        };
    }

    public function getSystemTemplates()
    {
        return ReportTemplate::where('is_system', true)->get();
    }

    public function getUserTemplates($userId)
    {
        return ReportTemplate::where('created_by', $userId)->get();
    }

    public function getFavoriteTemplates($userId)
    {
        return ReportTemplate::whereHas('favorites', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();
    }

    public function logActivity(string $action, ReportTemplate $template)
    {
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'action' => $action,
                'subject_type' => ReportTemplate::class,
                'subject_id' => $template->id,
                'properties' => [
                    'report_name' => $template->name,
                    'type' => $template->type,
                ]
            ]);
        }
    }
}
