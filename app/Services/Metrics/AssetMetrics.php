<?php

namespace App\Services\Metrics;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\AssetMaintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetMetrics implements \App\Contracts\MetricProvider
{
    public function cards(array $filters = []): array
    {
        $query = Asset::query();
        
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        
        $totalAssets = (clone $query)->count();
        $totalValue = (clone $query)->sum('purchase_cost');
        $netBookValue = (clone $query)->sum('current_book_value');
        $accumulatedDepreciation = (clone $query)->sum('accumulated_depreciation');
        
        $activeAssets = (clone $query)->where('status', 'Active')->count();
        $underMaintenance = (clone $query)->where('status', 'Under Maintenance')->count();

        $currentMonthDepreciation = AssetDepreciation::where('status', 'Posted')
            ->whereYear('period_date', now()->year)
            ->whereMonth('period_date', now()->month);
            
        if (!empty($filters['company_id'])) {
            $currentMonthDepreciation->whereHas('asset', function($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }
        $monthlyDepreciation = $currentMonthDepreciation->sum('amount');

        return [
            [
                'title' => 'Total Assets',
                'value' => number_format($totalAssets),
                'icon' => 'cube',
                'color' => 'blue',
            ],
            [
                'title' => 'Net Book Value',
                'value' => number_format($netBookValue, 2),
                'icon' => 'currency-dollar',
                'color' => 'green',
            ],
            [
                'title' => 'Monthly Depreciation',
                'value' => number_format($monthlyDepreciation, 2),
                'icon' => 'chart-bar',
                'color' => 'red',
            ],
            [
                'title' => 'Under Maintenance',
                'value' => number_format($underMaintenance),
                'icon' => 'wrench',
                'color' => 'yellow',
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
