<?php

namespace App\Services\Metrics;

use App\Models\Quotation;

class QuotationMetrics
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id ?? 1;

        $query = Quotation::where('company_id', $companyId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        $totalQuotations = (clone $query)->count();
        $draftQuotations = (clone $query)->whereHas('status', function($q) {
            $q->where('name', 'Draft');
        })->count();
        $approvedQuotations = (clone $query)->whereHas('status', function($q) {
            $q->where('name', 'Approved');
        })->count();
        $pendingQuotations = (clone $query)->whereHas('status', function($q) {
            $q->whereIn('name', ['Pending Approval', 'Pending']);
        })->count();
        $acceptedQuotations = (clone $query)->whereHas('status', function($q) {
            $q->where('name', 'Accepted');
        })->count();
        $revenueForecast = (clone $query)->whereHas('status', function($q) {
            $q->whereIn('name', ['Approved', 'Accepted', 'Sent']);
        })->sum('grand_total');

        return [
            'total_quotations' => $totalQuotations,
            'draft_quotations' => $draftQuotations,
            'approved_quotations' => $approvedQuotations,
            'pending_quotations' => $pendingQuotations,
            'accepted_quotations' => $acceptedQuotations,
            'revenue_forecast' => $revenueForecast,
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id ?? 1;

        return [
            'recent_quotations' => Quotation::where('company_id', $companyId)
                ->with('status')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        // Data for Quotation Summary, Conversion Rate, etc.
        return [];
    }
}
