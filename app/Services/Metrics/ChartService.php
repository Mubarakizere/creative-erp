<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class ChartService
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get all datasets required for dashboard charts.
     */
    public function getChartData(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_charts_{$userId}_{$companyId}{$filterHash}";
        
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.charts', 600);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            return [
                'tasksByStatus' => $this->tasksByStatus($filters),
                'tasksByPriority' => $this->tasksByPriority($filters),
                'projectProgress' => $this->projectProgress($filters),
                
                // Historical placeholders (would ideally be filtered queries as well)
                'tasksPerProject' => [12, 19, 3, 5, 2, 3],
                'monthlyTaskCompletion' => [65, 59, 80, 81, 56, 55, 40],
                'commentsPerModule' => [30, 40, 15, 15],
                'commentsPerUser' => [12, 19, 14, 5, 2],
                'dailyDiscussions' => [5, 10, 15, 8, 12, 20, 25],
                'monthlyDiscussions' => [50, 60, 45, 70, 90, 80],
                'mentionsPerMonth' => [10, 15, 5, 20, 25, 30],
                
                // Meeting Charts
                'meetingsPerMonth' => [4, 8, 15, 12, 20, 18, 25],
                'meetingsByType' => [10, 5, 8, 3, 2, 4],
                'attendanceRate' => [95, 92, 88, 96, 90],
                
                // Financial Charts
                'revenueTrends' => $this->revenueTrends($filters),
                'expenseTrends' => $this->expenseTrends($filters),
                'profitTrends' => $this->profitTrends($filters),

                // Inventory Charts
                'inventoryValueTrend' => [1000, 1500, 1200, 1800, 2000, 1900], // Placeholder
                'warehouseDistribution' => $this->warehouseDistribution($filters),
                'categoryDistribution' => $this->categoryDistribution($filters),
                'stockMovement' => [20, 35, 10, 40, 50, 15], // Placeholder

                // Procurement Charts
                'purchaseTrends' => $this->purchaseTrends($filters),
                'supplierSpendChart' => $this->supplierSpendChart($filters),
                'leadTimeChart' => $this->leadTimeChart($filters),
            ];
        });
    }

    private function applyFilters($query, array $filters, string $relation = null)
    {
        $prefix = $relation ? $relation . '.' : '';

        if (!empty($filters['company_id'])) {
            $query->whereIn($prefix . 'company_id', (array) $filters['company_id']);
        }
        if (!empty($filters['branch_id'])) {
            $query->whereIn($prefix . 'branch_id', (array) $filters['branch_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereIn($prefix . 'department_id', (array) $filters['department_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate($prefix . 'created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate($prefix . 'created_at', '<=', $filters['date_to']);
        }
        if (!empty($filters['project_id'])) {
            $query->whereIn($prefix . 'project_id', (array) $filters['project_id']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->whereIn($prefix . 'assigned_to', (array) $filters['assigned_to']);
        }
        
        return $query;
    }

    private function tasksByStatus(array $filters = []): array
    {
        return [
            'Pending' => $this->applyFilters(\App\Models\Task::where('status', 'Pending'), $filters)->count(),
            'In Progress' => $this->applyFilters(\App\Models\Task::where('status', 'In Progress'), $filters)->count(),
            'Waiting Review' => $this->applyFilters(\App\Models\Task::where('status', 'Waiting Review'), $filters)->count(),
            'Completed' => $this->applyFilters(\App\Models\Task::where('status', 'Completed'), $filters)->count(),
            'On Hold' => $this->applyFilters(\App\Models\Task::where('status', 'On Hold'), $filters)->count(),
        ];
    }

    private function tasksByPriority(array $filters = []): array
    {
        return [
            'Low' => $this->applyFilters(\App\Models\Task::where('priority', 'Low'), $filters)->count(),
            'Medium' => $this->applyFilters(\App\Models\Task::where('priority', 'Medium'), $filters)->count(),
            'High' => $this->applyFilters(\App\Models\Task::where('priority', 'High'), $filters)->count(),
            'Critical' => $this->applyFilters(\App\Models\Task::where('priority', 'Critical'), $filters)->count(),
        ];
    }

    private function projectProgress(array $filters = []): array
    {
        $query = \App\Models\Project::whereIn('status', ['Planning', 'In Progress'])->take(5);
        $this->applyFilters($query, $filters);
        
        $projects = $query->pluck('progress', 'name')->toArray();
        return !empty($projects) ? $projects : ['No Active Projects' => 0];
    }

    private function revenueTrends(array $filters = []): array
    {
        return $this->getMonthlyTrend('Revenue', $filters);
    }

    private function expenseTrends(array $filters = []): array
    {
        return $this->getMonthlyTrend('Expense', $filters);
    }

    private function profitTrends(array $filters = []): array
    {
        $revenue = $this->revenueTrends($filters);
        $expense = $this->expenseTrends($filters);
        $profit = [];
        for ($i = 0; $i < 6; $i++) {
            $profit[] = ($revenue[$i] ?? 0) - ($expense[$i] ?? 0);
        }
        return $profit;
    }

    private function getMonthlyTrend(string $category, array $filters = []): array
    {
        $query = \App\Models\GeneralLedger::select(
            \Illuminate\Support\Facades\DB::raw("strftime('%m', date) as month"),
            \Illuminate\Support\Facades\DB::raw("SUM(credit - debit) as net_credit"),
            \Illuminate\Support\Facades\DB::raw("SUM(debit - credit) as net_debit")
        )
        ->whereHas('chartOfAccount.accountType', function($q) use ($category) {
            $q->where('category', $category);
        })
        ->whereDate('date', '>=', now()->subMonths(5)->startOfMonth())
        ->whereDate('date', '<=', now()->endOfMonth())
        ->groupBy('month')
        ->orderBy('month');

        $this->applyFilters($query, $filters);
        $results = $query->get()->keyBy('month');

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthObj = now()->subMonths($i);
            $monthNum = $monthObj->format('m');
            $row = $results->get($monthNum);
            
            // Revenues have natural credit balance, Expenses have natural debit balance
            $value = 0;
            if ($row) {
                $value = $category === 'Revenue' ? $row->net_credit : $row->net_debit;
            }
            $trend[] = max(0, (float) $value);
        }
        return $trend;
    }

    public function departmentPerformance(array $filters = []): array
    {
        return $this->getPerformanceBy('department_id', $filters);
    }

    public function branchPerformance(array $filters = []): array
    {
        return $this->getPerformanceBy('branch_id', $filters);
    }

    private function getPerformanceBy(string $groupBy, array $filters = []): array
    {
        // Calculate Net Profit = Revenue - Expense grouped by $groupBy
        $query = \App\Models\GeneralLedger::select(
            "journals.{$groupBy}",
            \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN account_types.category = 'Revenue' THEN general_ledgers.credit - general_ledgers.debit ELSE 0 END) as revenue"),
            \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN account_types.category = 'Expense' THEN general_ledgers.debit - general_ledgers.credit ELSE 0 END) as expenses")
        )
        ->join('journal_entries', 'general_ledgers.journal_entry_id', '=', 'journal_entries.id')
        ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
        ->join('chart_of_accounts', 'general_ledgers.chart_of_account_id', '=', 'chart_of_accounts.id')
        ->join('account_types', 'chart_of_accounts.account_type_id', '=', 'account_types.id')
        ->whereIn('account_types.category', ['Revenue', 'Expense'])
        ->whereNotNull("journals.{$groupBy}")
        ->groupBy("journals.{$groupBy}");

        $this->applyFilters($query, $filters, 'journals');
        
        $results = $query->get()->map(function($row) {
            return [
                'id' => $row->department_id ?? $row->branch_id,
                'revenue' => (float) $row->revenue,
                'expenses' => (float) $row->expenses,
                'net_profit' => (float) ($row->revenue - $row->expenses)
            ];
        });

        // Resolve names
        if ($groupBy === 'department_id') {
            $results = $results->map(function($row) {
                $dept = \App\Models\Department::find($row['id']);
                $row['name'] = $dept ? $dept->name : 'Unknown';
                return $row;
            });
        } else {
            $results = $results->map(function($row) {
                $branch = \App\Models\Branch::find($row['id']);
                $row['name'] = $branch ? $branch->name : 'Unknown';
                return $row;
            });
        }

        return $results->sortByDesc('net_profit')->values()->toArray();
    }

    private function warehouseDistribution(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        $warehouses = \App\Models\Warehouse::withSum('inventories', 'available_quantity');
        
        if ($companyId) {
            $warehouses->where('company_id', $companyId);
        }
        
        $data = $warehouses->get()->mapWithKeys(function ($w) {
            return [$w->name => $w->inventories_sum_available_quantity ?? 0];
        });
        
        return empty($data) ? ['Main Warehouse' => 0] : $data->toArray();
    }

    private function categoryDistribution(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        $categories = \App\Models\ProductCategory::withCount('products');
        
        if ($companyId) {
            $categories->where('company_id', $companyId);
        }
        
        $data = $categories->get()->mapWithKeys(function ($c) {
            return [$c->name => $c->products_count ?? 0];
        });
        
        return empty($data) ? ['General' => 0] : $data->toArray();
    }

    private function purchaseTrends(array $filters = []): array
    {
        $query = \App\Models\PurchaseOrder::select(
            \Illuminate\Support\Facades\DB::raw("strftime('%m', order_date) as month"),
            \Illuminate\Support\Facades\DB::raw("SUM(grand_total) as total_value")
        )
        ->whereNotIn('status', ['draft', 'cancelled'])
        ->whereDate('order_date', '>=', now()->subMonths(5)->startOfMonth())
        ->whereDate('order_date', '<=', now()->endOfMonth())
        ->groupBy('month')
        ->orderBy('month');

        $this->applyFilters($query, $filters);
        $results = $query->get()->keyBy('month');

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthObj = now()->subMonths($i);
            $monthNum = $monthObj->format('m');
            $row = $results->get($monthNum);
            $trend[] = $row ? (float) $row->total_value : 0;
        }
        return $trend;
    }

    private function supplierSpendChart(array $filters = []): array
    {
        $query = \App\Models\SupplierPayment::select(
            'supplier_id',
            \Illuminate\Support\Facades\DB::raw("SUM(amount) as total_spend")
        )
        ->groupBy('supplier_id')
        ->orderByDesc('total_spend')
        ->take(5);

        $this->applyFilters($query, $filters);
        $results = $query->with('supplier')->get();

        $data = [];
        foreach ($results as $row) {
            $name = $row->supplier ? $row->supplier->name : 'Unknown';
            $data[$name] = (float) $row->total_spend;
        }
        return empty($data) ? ['No Data' => 0] : $data;
    }

    private function leadTimeChart(array $filters = []): array
    {
        $query = \App\Models\PurchaseOrder::with(['supplier', 'goodsReceipts'])
            ->where('status', 'completed')
            ->whereHas('goodsReceipts');
            
        $this->applyFilters($query, $filters);
        $orders = $query->get();
        
        $suppliers = [];
        foreach ($orders as $order) {
            $supplierId = $order->supplier_id;
            
            if (!isset($suppliers[$supplierId])) {
                $suppliers[$supplierId] = [
                    'name' => $order->supplier ? $order->supplier->name : 'Unknown',
                    'total_days' => 0,
                    'count' => 0,
                ];
            }
            
            $firstReceipt = $order->goodsReceipts->sortBy('receipt_date')->first();
            if ($firstReceipt && $order->order_date) {
                $days = $order->order_date->diffInDays($firstReceipt->receipt_date);
                $suppliers[$supplierId]['total_days'] += max(0, $days);
                $suppliers[$supplierId]['count']++;
            }
        }
        
        $data = [];
        foreach ($suppliers as $s) {
            if ($s['count'] > 0) {
                $data[$s['name']] = round($s['total_days'] / $s['count'], 2);
            }
        }
        
        arsort($data);
        $data = array_slice($data, 0, 5, true); // Top 5
        return empty($data) ? ['No Data' => 0] : $data;
    }
}
