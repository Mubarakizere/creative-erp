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
        return [
            'paymentTrends' => $this->paymentTrends($filters)
        ];
    }

    public function paymentTrends(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? (auth()->user() ? auth()->user()->company_id : null);
        if (!$companyId) return [];

        $query = Payment::select(
            \Illuminate\Support\Facades\DB::raw("strftime('%m', payment_date) as month"),
            \Illuminate\Support\Facades\DB::raw("SUM(amount) as total")
        )
        ->where('company_id', $companyId)
        ->where('status', 'Completed')
        ->whereDate('payment_date', '>=', now()->subMonths(5)->startOfMonth())
        ->whereDate('payment_date', '<=', now()->endOfMonth())
        ->groupBy('month')
        ->orderBy('month');

        $results = $query->get()->keyBy('month');

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthObj = now()->subMonths($i);
            $monthNum = $monthObj->format('m');
            $row = $results->get($monthNum);
            $trend[] = max(0, (float) ($row->total ?? 0));
        }

        return $trend;
    }
}
