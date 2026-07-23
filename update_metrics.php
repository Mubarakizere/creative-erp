<?php

$dir = 'app/Services/Metrics';

$files = [
    'ProcurementMetrics.php' => <<<'EOT'
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
            ['title' => 'Pending Requisitions', 'value' => $pendingRequisitions, 'icon' => 'shopping-cart'],
            ['title' => 'Pending POs', 'value' => $pendingPOs, 'icon' => 'file-text'],
            ['title' => 'Total Purchase Value', 'value' => number_format($purchaseValue, 2), 'icon' => 'dollar-sign'],
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
EOT,
    'SupplierMetrics.php' => <<<'EOT'
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
            ['title' => 'Total Suppliers', 'value' => $totalSuppliers, 'icon' => 'users'],
            ['title' => 'Outstanding Supplier Payments', 'value' => number_format($outstandingPayments, 2), 'icon' => 'alert-circle'],
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
EOT,
];

foreach ($files as $name => $content) {
    file_put_contents("$dir/$name", $content);
}

// Update MetricsService.php
$metricsServicePath = "$dir/MetricsService.php";
$content = file_get_contents($metricsServicePath);

// Add dependencies to constructor signature
$searchStr = 'WarehouseMetrics $warehouseMetrics';
$replaceStr = "WarehouseMetrics \$warehouseMetrics,\n        ProcurementMetrics \$procurementMetrics,\n        SupplierMetrics \$supplierMetrics";
$content = str_replace($searchStr, $replaceStr, $content);

// Add to array
$searchStr2 = '$warehouseMetrics';
$replaceStr2 = "\$warehouseMetrics,\n            \$procurementMetrics,\n            \$supplierMetrics";
$content = str_replace($searchStr2, $replaceStr2, $content);

file_put_contents($metricsServicePath, $content);
echo "Metrics updated\n";
