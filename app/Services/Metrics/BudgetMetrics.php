<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Budget;

class BudgetMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        if (!auth()->user() || !auth()->user()->can('budget.view')) {
            return [];
        }

        $companyId = $filters['company_id'] ?? auth()->user()->company_id;
        
        $query = Budget::where('company_id', $companyId);
        
        $activeBudgets = (clone $query)->where('status', 'active')->count();
        $totalBudgetAmount = (clone $query)->where('status', 'active')->sum('total_amount');

        return [
            [
                'title' => 'Active Budgets',
                'value' => $activeBudgets,
                'icon' => 'document-chart-bar',
                'color' => 'purple',
            ],
            [
                'title' => 'Total Budget Allocated',
                'value' => number_format($totalBudgetAmount, 2),
                'icon' => 'currency-dollar',
                'color' => 'blue',
            ]
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
