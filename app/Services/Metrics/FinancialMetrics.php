<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Company;
use App\Services\Finance\FinancialStatementService;

class FinancialMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        // These are more granular metrics not just rollups.
        if (!auth()->user() || !auth()->user()->can('financial.view')) {
            return [];
        }

        $companyId = $filters['company_id'] ?? auth()->user()->company_id;
        if (!$companyId) return [];

        $financialService = app(FinancialStatementService::class);
        $balanceSheet = $financialService->generateBalanceSheet($companyId);
        $pnl = $financialService->generateProfitAndLoss($companyId);

        $currentAssets = $balanceSheet['assets']['current']['total'] ?? 0;
        $currentLiabilities = $balanceSheet['liabilities']['current']['total'] ?? 0;
        $revenue = $pnl['revenue']['total'] ?? 0;
        $netProfit = $pnl['net_profit'] ?? 0;
        $operatingIncome = $pnl['operating_income'] ?? 0;
        
        $currentRatio = $currentLiabilities > 0 ? ($currentAssets / $currentLiabilities) : 0;
        $profitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        $operatingMargin = $revenue > 0 ? ($operatingIncome / $revenue) * 100 : 0;

        return [
            [
                'title' => 'Current Ratio',
                'value' => round($currentRatio, 2),
                'icon' => 'scale',
                'color' => 'blue',
            ],
            [
                'title' => 'Profit Margin',
                'value' => round($profitMargin, 2) . '%',
                'icon' => 'percentage',
                'color' => 'green',
            ],
            [
                'title' => 'Operating Margin',
                'value' => round($operatingMargin, 2) . '%',
                'icon' => 'chart-bar',
                'color' => 'indigo',
            ],
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
