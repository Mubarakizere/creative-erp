<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Invoice;

class ReceivableMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $query = Invoice::where('status', '!=', 'Cancelled')->where('status', '!=', 'Voided');
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $totalReceivables = (clone $query)->sum('balance_due');
        $overdueReceivables = (clone $query)->where('status', 'Overdue')->sum('balance_due');
        
        $aging30 = (clone $query)->where('status', 'Overdue')->where('due_date', '>=', now()->subDays(30))->sum('balance_due');
        $aging60 = (clone $query)->where('status', 'Overdue')->where('due_date', '<', now()->subDays(30))->where('due_date', '>=', now()->subDays(60))->sum('balance_due');
        $aging90 = (clone $query)->where('status', 'Overdue')->where('due_date', '<', now()->subDays(60))->sum('balance_due');
        
        $totalInvoiced = (clone $query)->sum('total_amount');
        $collectionRate = $totalInvoiced > 0 ? (($totalInvoiced - $totalReceivables) / $totalInvoiced) * 100 : 0;

        return [
            'total_receivables' => [
                'label' => 'Total Receivables',
                'value' => '$' . number_format($totalReceivables, 2),
                'icon' => 'credit-card',
                'color' => 'indigo',
            ],
            'overdue_receivables' => [
                'label' => 'Overdue Receivables',
                'value' => '$' . number_format($overdueReceivables, 2),
                'icon' => 'exclamation',
                'color' => 'red',
            ],
            'collection_rate' => [
                'label' => 'Collection Rate',
                'value' => number_format($collectionRate, 1) . '%',
                'icon' => 'check-circle',
                'color' => 'teal',
            ],
            'aging_30_days' => [
                'label' => 'Overdue (1-30 Days)',
                'value' => '$' . number_format($aging30, 2),
                'icon' => 'clock',
                'color' => 'yellow',
            ],
            'aging_90_days' => [
                'label' => 'Overdue (60+ Days)',
                'value' => '$' . number_format($aging90 + $aging60, 2),
                'icon' => 'exclamation-circle',
                'color' => 'red',
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
