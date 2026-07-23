<?php
namespace App\Services\Metrics;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\GoodsReceipt;
use App\Models\PurchaseInvoice;

class ProcurementMetrics implements \App\Contracts\MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $pendingRequisitions = PurchaseRequisition::where('company_id', $companyId)
            ->where('status', 'submitted')->count();
            
        $pendingPOs = PurchaseOrder::where('company_id', $companyId)
            ->where('status', 'approved')->count();
            
        $purchaseValue = PurchaseOrder::where('company_id', $companyId)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('grand_total');

        return [
            'pending_requisitions' => ['title' => 'Pending Requisitions', 'value' => $pendingRequisitions, 'icon' => 'shopping-cart'],
            'pending_pos' => ['title' => 'Pending POs', 'value' => $pendingPOs, 'icon' => 'file-text'],
            'total_purchase_value' => ['title' => 'Total Purchase Value', 'value' => number_format($purchaseValue, 2), 'icon' => 'dollar-sign'],
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