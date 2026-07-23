<?php
namespace App\Services\Metrics;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\PurchaseInvoice;

class SupplierMetrics implements \App\Contracts\MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $totalSuppliers = Supplier::where('company_id', $companyId)->count();
            
        $outstandingPayments = PurchaseInvoice::where('company_id', $companyId)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->sum(\Illuminate\Support\Facades\DB::raw('grand_total - paid_amount'));

        return [
            'total_suppliers' => ['title' => 'Total Suppliers', 'value' => $totalSuppliers, 'icon' => 'users'],
            'outstanding_supplier_payments' => ['title' => 'Outstanding Supplier Payments', 'value' => number_format($outstandingPayments, 2), 'icon' => 'alert-circle'],
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