<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

class InvoiceMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $query = Invoice::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $total = (clone $query)->count();
        $draft = (clone $query)->where('status', 'Draft')->count();
        $unpaid = (clone $query)->whereIn('status', ['Issued', 'Sent', 'Viewed'])->count();
        $overdue = (clone $query)->where('status', 'Overdue')->count();

        return [
            'total_invoices' => [
                'label' => 'Total Invoices',
                'value' => $total,
                'icon' => 'document-text',
                'color' => 'blue',
            ],
            'draft_invoices' => [
                'label' => 'Draft Invoices',
                'value' => $draft,
                'icon' => 'document',
                'color' => 'gray',
            ],
            'unpaid_invoices' => [
                'label' => 'Unpaid Invoices',
                'value' => $unpaid,
                'icon' => 'clock',
                'color' => 'yellow',
            ],
            'overdue_invoices' => [
                'label' => 'Overdue Invoices',
                'value' => $overdue,
                'icon' => 'exclamation-circle',
                'color' => 'red',
            ],
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        $query = Invoice::with('client')->latest()->take(5);
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        return [
            'recent_invoices' => $query->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
