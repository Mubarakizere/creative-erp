<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class MetricsService
{
    /**
     * @var \App\Contracts\MetricProvider[]
     */
    protected array $providers = [];

    public function __construct(
        DashboardMetrics $dashboardMetrics,
        OrganizationMetrics $organizationMetrics,
        UserMetrics $userMetrics,
        ClientMetrics $clientMetrics,
        ProjectMetrics $projectMetrics,
        TaskMetrics $taskMetrics,
        MeetingMetrics $meetingMetrics,
        TimeMetrics $timeMetrics,
        DocumentMetrics $documentMetrics,
        DiscussionMetrics $discussionMetrics,
        WorkflowMetrics $workflowMetrics,
        NotificationMetrics $notificationMetrics,
        AnnouncementMetrics $announcementMetrics,
        CrmMetrics $crmMetrics,
        QuotationMetrics $quotationMetrics,
        InvoiceMetrics $invoiceMetrics,
        PaymentMetrics $paymentMetrics,
        ReceivableMetrics $receivableMetrics,
        AccountingMetrics $accountingMetrics,
        FinancialMetrics $financialMetrics,
        BudgetMetrics $budgetMetrics,
        ExecutiveMetrics $executiveMetrics,
        InventoryMetrics $inventoryMetrics,
        WarehouseMetrics $warehouseMetrics
    ) {
        $this->providers = [
            $dashboardMetrics,
            $organizationMetrics,
            $userMetrics,
            $clientMetrics,
            $projectMetrics,
            $taskMetrics,
            $meetingMetrics,
            $timeMetrics,
            $documentMetrics,
            $discussionMetrics,
            $workflowMetrics,
            $notificationMetrics,
            $announcementMetrics,
            $crmMetrics,
            $quotationMetrics,
            $invoiceMetrics,
            $paymentMetrics,
            $receivableMetrics,
            $accountingMetrics,
            $financialMetrics,
            $budgetMetrics,
            $executiveMetrics,
            $inventoryMetrics,
            $warehouseMetrics
        ];
    }

    /**
     * Get aggregated dashboard statistics (cards).
     */
    public function cards(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_cards_{$userId}_{$companyId}{$filterHash}";
        
        // Use a shorter TTL (60s) for heavily filtered requests, else standard dashboard TTL
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.dashboard', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            $cards = [];
            foreach ($this->providers as $provider) {
                $cards = array_merge($cards, $provider->cards($filters));
            }
            return $cards;
        });
    }

    /**
     * Get aggregated dashboard widgets data.
     */
    public function widgets(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_widgets_{$userId}_{$companyId}{$filterHash}";
        
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.dashboard', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            $widgets = [];
            foreach ($this->providers as $provider) {
                $widgets = array_merge($widgets, $provider->widgets($filters));
            }
            return $widgets;
        });
    }

    /**
     * Get aggregated report data.
     */
    public function reports(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_reports_{$userId}_{$companyId}{$filterHash}";
        
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.reports', 900);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            $reports = [];
            foreach ($this->providers as $provider) {
                $reports = array_merge($reports, $provider->reports($filters));
            }
            return $reports;
        });
    }

    public function dashboard(array $filters = []): array
    {
        // Inject ChartService here to avoid circular dependency in constructor if any,
        // though ChartService will depend on MetricsService, not the other way around.
        $chartService = app(ChartService::class);

        return array_merge([
            'stats' => $this->cards($filters),
            'chartData' => $chartService->getChartData($filters),
        ], $this->widgets($filters));
    }
}
