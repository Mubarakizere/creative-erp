<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Payment;

class PaymentMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $query = Payment::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $totalAmount = (clone $query)->where('status', 'Completed')->sum('amount');
        $thisMonth = (clone $query)->where('status', 'Completed')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount');
        $today = (clone $query)->where('status', 'Completed')->whereDate('payment_date', now()->toDateString())->sum('amount');

        return [
            'total_payments' => [
                'label' => 'Total Revenue',
                'value' => '$' . number_format($totalAmount, 2),
                'icon' => 'currency-dollar',
                'color' => 'green',
            ],
            'revenue_this_month' => [
                'label' => 'Revenue This Month',
                'value' => '$' . number_format($thisMonth, 2),
                'icon' => 'chart-bar',
                'color' => 'emerald',
            ],
            'revenue_today' => [
                'label' => 'Revenue Today',
                'value' => '$' . number_format($today, 2),
                'icon' => 'currency-dollar',
                'color' => 'teal',
            ],
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        // In the migration we have payment allocations, but let's just grab client
        $query = Payment::with(['client'])->latest()->take(5);
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        return [
            'recent_payments' => $query->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
