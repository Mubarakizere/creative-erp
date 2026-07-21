<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class ReportMetrics
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get datasets for various report summaries.
     */
    public function getReportSummaries(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_report_summaries_{$userId}_{$companyId}{$filterHash}";
        
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.reports', 900);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            // Consume data from MetricsService to avoid duplicated calculations
            $cards = $this->metricsService->cards($filters);
            $reports = $this->metricsService->reports($filters);

            return [
                'project_summary' => [
                    'total' => $cards['projects'] ?? 0,
                    'active' => $cards['active_projects'] ?? 0,
                    'completed' => $cards['completed_projects'] ?? 0,
                ],
                'task_summary' => [
                    'total' => $cards['total_tasks'] ?? 0,
                    'completed' => $cards['completed_tasks'] ?? 0,
                    'overdue' => $cards['overdue_tasks'] ?? 0,
                ],
                'meeting_summary' => [
                    'today' => $cards['meetings_today'] ?? 0,
                    'upcoming' => $cards['upcoming_meetings'] ?? 0,
                ],
                'time_summary' => [
                    'hours_this_week' => $cards['hours_this_week'] ?? 0,
                    'billable_month' => $cards['billable_hours_month'] ?? 0,
                ],
                'client_summary' => [
                    'total' => $cards['clients'] ?? 0,
                ],
                'organization_summary' => [
                    'companies' => $cards['companies'] ?? 0,
                    'branches' => $cards['branches'] ?? 0,
                    'departments' => $cards['departments'] ?? 0,
                ],
                'quotation_summary' => [
                    'total' => $cards['total_quotations'] ?? 0,
                    'draft' => $cards['draft_quotations'] ?? 0,
                    'pending' => $cards['pending_quotations'] ?? 0,
                    'approved' => $cards['approved_quotations'] ?? 0,
                    'accepted' => $cards['accepted_quotations'] ?? 0,
                ],
                'sales_forecast' => [
                    'revenue_forecast' => $cards['revenue_forecast'] ?? 0,
                    'pipeline_value' => $cards['pipeline_value'] ?? 0,
                    'won_deals' => $cards['won_deals'] ?? 0,
                    'lost_deals' => $cards['lost_deals'] ?? 0,
                ],
                'approval_summary' => [
                    'pending_approvals' => $cards['pending_approvals'] ?? 0,
                    'approved_today' => $cards['approved_today'] ?? 0,
                    'rejected_today' => $cards['rejected_today'] ?? 0,
                    'my_pending_requests' => $cards['my_pending_requests'] ?? 0,
                ],
                'invoice_summary' => [
                    'total' => $cards['total_invoices']['value'] ?? 0,
                    'draft' => $cards['draft_invoices']['value'] ?? 0,
                    'unpaid' => $cards['unpaid_invoices']['value'] ?? 0,
                    'overdue' => $cards['overdue_invoices']['value'] ?? 0,
                ],
                'payment_summary' => [
                    'total_revenue' => $cards['total_payments']['value'] ?? 0,
                    'revenue_this_month' => $cards['revenue_this_month']['value'] ?? 0,
                ],
                'receivable_summary' => [
                    'total_receivables' => $cards['total_receivables']['value'] ?? 0,
                    'overdue_receivables' => $cards['overdue_receivables']['value'] ?? 0,
                    'collection_rate' => $cards['collection_rate']['value'] ?? 0,
                ],
            ];
        });
    }
}
