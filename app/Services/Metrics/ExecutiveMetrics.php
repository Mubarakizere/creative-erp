<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Services\Finance\FinancialStatementService;

class ExecutiveMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        if (!app()->runningInConsole() && (!auth()->user() || !auth()->user()->can('executive.dashboard'))) {
            return [];
        }

        $companyId = $filters['company_id'] ?? (auth()->user() ? auth()->user()->company_id : null);
        if (!$companyId) return [];

        $financialService = app(FinancialStatementService::class);
        $pnl = $financialService->generateProfitAndLoss($companyId);
        $cashFlow = $financialService->generateCashFlowStatement($companyId);

        $revenue = $pnl['revenue']['total'] ?? 0;
        $expenses = $pnl['operating_expenses']['total'] ?? 0;
        $netProfit = $pnl['net_profit'] ?? 0;
        $cash = $cashFlow['closing_cash'] ?? 0;

        // Health score can be a simple calculation based on profit margins and cash flow
        $healthScore = 100;
        if ($revenue > 0) {
            $margin = $netProfit / $revenue;
            if ($margin < 0) $healthScore -= 30;
            elseif ($margin < 0.1) $healthScore -= 10;
        } else {
            $healthScore = 0;
        }
        if ($cashFlow['net_cash_flow'] < 0) {
            $healthScore -= 20;
        }

        // Outstanding Receivables
        $outstandingReceivables = \App\Models\Invoice::where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Voided')
            ->where('company_id', $companyId)
            ->sum('balance_due');

        // Budget vs Actual (using expense budget as a proxy for actuals)
        $totalBudget = \App\Models\Budget::where('company_id', $companyId)->where('status', 'active')->sum('total_amount');
        $budgetVariance = $totalBudget - $expenses;

        return [
            [
                'title' => 'Total Revenue',
                'value' => number_format($revenue, 2),
                'icon' => 'arrow-trending-up',
                'color' => 'green',
            ],
            [
                'title' => 'Total Expenses',
                'value' => number_format($expenses, 2),
                'icon' => 'arrow-trending-down',
                'color' => 'red',
            ],
            [
                'title' => 'Net Profit',
                'value' => number_format($netProfit, 2),
                'icon' => 'banknotes',
                'color' => 'indigo',
            ],
            [
                'title' => 'Cash Position',
                'value' => number_format($cash, 2),
                'icon' => 'wallet',
                'color' => 'blue',
            ],
            [
                'title' => 'Outstanding Receivables',
                'value' => number_format($outstandingReceivables, 2),
                'icon' => 'credit-card',
                'color' => 'orange',
            ],
            [
                'title' => 'Budget vs Actual',
                'value' => number_format($budgetVariance, 2) . ' Variance',
                'icon' => 'chart-pie',
                'color' => $budgetVariance >= 0 ? 'green' : 'red',
            ],
            [
                'title' => 'Health Score',
                'value' => max(0, $healthScore) . '/100',
                'icon' => 'heart',
                'color' => $healthScore > 70 ? 'green' : ($healthScore > 40 ? 'yellow' : 'red'),
            ]
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? (auth()->user() ? auth()->user()->company_id : null);
        if (!$companyId) return [];

        // Top Customers by Revenue
        $topCustomers = \App\Models\Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('client_id, SUM(total_amount) as total_revenue')
            ->groupBy('client_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('client')
            ->get()
            ->map(function ($invoice) {
                return [
                    'name' => $invoice->client->display_name ?? 'Unknown',
                    'revenue' => $invoice->total_revenue
                ];
            });

        // Top Revenue Projects
        $topProjects = \App\Models\Invoice::where('company_id', $companyId)
            ->whereNotNull('project_id')
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('project_id, SUM(total_amount) as total_revenue')
            ->groupBy('project_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('project')
            ->get()
            ->map(function ($invoice) {
                return [
                    'name' => $invoice->project->name ?? 'Unknown',
                    'revenue' => $invoice->total_revenue
                ];
            });

        return [
            'top_customers' => $topCustomers,
            'top_projects' => $topProjects
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
