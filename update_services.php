<?php

$dir = 'app/Services';

if (!is_dir($dir . '/Procurement')) {
    mkdir($dir . '/Procurement', 0755, true);
}

$services = [
    'Procurement/SupplierService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Supplier::with(['company', 'category', 'paymentTerm']);
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->latest()->paginate(15);
    }

    public function create(array $data): Supplier
    {
        $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
        $data['created_by'] = auth()->id();
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $data['updated_by'] = auth()->id();
        $supplier->update($data);
        return $supplier;
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}
EOT,
    'Procurement/PurchaseRequisitionService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\DB;
use App\Services\WorkflowService;

class PurchaseRequisitionService
{
    public function list(array $filters = [])
    {
        $query = PurchaseRequisition::with(['requestedBy', 'department', 'project']);
        
        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }
        
        return $query->latest()->paginate(15);
    }

    public function create(array $data, array $items): PurchaseRequisition
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            $data['requested_by'] = auth()->id();
            $data['status'] = 'draft';
            
            $pr = PurchaseRequisition::create($data);
            
            foreach ($items as $item) {
                $pr->items()->create($item);
            }
            
            return $pr;
        });
    }

    public function submit(PurchaseRequisition $pr)
    {
        $pr->update(['status' => 'submitted']);
        // Trigger workflow
        return $pr;
    }

    public function approve(PurchaseRequisition $pr)
    {
        if ($pr->requested_by === auth()->id()) {
            throw new \Exception("Cannot approve own requisition");
        }
        $pr->update(['status' => 'approved']);
        return $pr;
    }
}
EOT,
    'Procurement/PurchaseOrderService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function list(array $filters = [])
    {
        $query = PurchaseOrder::with(['supplier']);
        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }
        return $query->latest()->paginate(15);
    }

    public function create(array $data, array $items): PurchaseOrder
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            $data['status'] = 'draft';
            
            $po = PurchaseOrder::create($data);
            
            foreach ($items as $item) {
                $po->items()->create($item);
            }
            
            return $po;
        });
    }

    public function approve(PurchaseOrder $po)
    {
        if ($po->created_by === auth()->id()) {
            throw new \Exception("Cannot approve own purchase order");
        }
        $po->update(['status' => 'approved']);
        return $po;
    }
}
EOT,
    'Procurement/GoodsReceiptService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\Inventory\InventoryEngine;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;

class GoodsReceiptService
{
    protected InventoryEngine $inventoryEngine;
    protected JournalService $journalService;

    public function __construct(InventoryEngine $inventoryEngine, JournalService $journalService)
    {
        $this->inventoryEngine = $inventoryEngine;
        $this->journalService = $journalService;
    }

    public function create(array $data, array $items): GoodsReceipt
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            
            $gr = GoodsReceipt::create($data);
            
            $totalCost = 0;
            $po = PurchaseOrder::find($gr->purchase_order_id);

            foreach ($items as $item) {
                $grItem = $gr->items()->create($item);
                
                // Increase stock
                $product = $grItem->product;
                $warehouse = $gr->warehouse;
                $this->inventoryEngine->stockIn(
                    $product, 
                    $warehouse, 
                    $grItem->quantity_received, 
                    'goods_receipt', 
                    $gr
                );

                if ($po && $grItem->purchase_order_item_id) {
                    $poItem = $po->items()->find($grItem->purchase_order_item_id);
                    if ($poItem) {
                        $poItem->received_quantity += $grItem->quantity_received;
                        $poItem->save();
                        $totalCost += ($grItem->quantity_received * $poItem->unit_price);
                    }
                }
            }

            if ($po) {
                $allReceived = $po->items()->whereColumn('received_quantity', '<', 'quantity')->doesntExist();
                $po->update(['status' => $allReceived ? 'completed' : 'partially_received']);
            }

            // Create Journal Entry
            // Debit Inventory, Credit GRNI
            if ($totalCost > 0) {
                $inventoryAccountId = \App\Models\ChartOfAccount::where('code', '1200')->value('id') ?? \App\Models\ChartOfAccount::first()->id;
                $grniAccountId = \App\Models\ChartOfAccount::where('code', '2100')->value('id') ?? \App\Models\ChartOfAccount::first()->id;

                $this->journalService->createAutomaticJournal([
                    'company_id' => $gr->company_id,
                    'date' => $gr->receipt_date,
                    'memo' => 'Goods Receipt ' . $gr->code,
                ], [
                    [
                        'chart_of_account_id' => $inventoryAccountId,
                        'description' => 'Inventory Received',
                        'debit' => $totalCost,
                        'credit' => 0,
                    ],
                    [
                        'chart_of_account_id' => $grniAccountId,
                        'description' => 'Goods Receipt Not Invoiced',
                        'debit' => 0,
                        'credit' => $totalCost,
                    ]
                ]);
            }
            
            return $gr;
        });
    }
}
EOT,
    'Procurement/PurchaseInvoiceService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\PurchaseInvoice;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function create(array $data, array $items): PurchaseInvoice
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            
            $invoice = PurchaseInvoice::create($data);
            $totalCost = 0;

            foreach ($items as $item) {
                $invoiceItem = $invoice->items()->create($item);
                $totalCost += $invoiceItem->total;
            }

            // Debit GRNI (or Expense), Credit Accounts Payable
            if ($totalCost > 0) {
                $grniAccountId = \App\Models\ChartOfAccount::where('code', '2100')->value('id') ?? \App\Models\ChartOfAccount::first()->id;
                $apAccountId = \App\Models\ChartOfAccount::where('code', '2000')->value('id') ?? \App\Models\ChartOfAccount::first()->id;

                $this->journalService->createAutomaticJournal([
                    'company_id' => $invoice->company_id,
                    'date' => $invoice->invoice_date,
                    'memo' => 'Purchase Invoice ' . $invoice->invoice_number,
                ], [
                    [
                        'chart_of_account_id' => $grniAccountId,
                        'description' => 'Goods Receipt Invoiced',
                        'debit' => $totalCost,
                        'credit' => 0,
                    ],
                    [
                        'chart_of_account_id' => $apAccountId,
                        'description' => 'Accounts Payable',
                        'debit' => 0,
                        'credit' => $totalCost,
                    ]
                ]);
            }

            return $invoice;
        });
    }
}
EOT,
    'Procurement/SupplierPaymentService.php' => <<<'EOT'
<?php
namespace App\Services\Procurement;

use App\Models\SupplierPayment;
use App\Models\PurchaseInvoice;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;

class SupplierPaymentService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function create(array $data): SupplierPayment
    {
        return DB::transaction(function() use ($data) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            
            $payment = SupplierPayment::create($data);
            
            if ($payment->purchase_invoice_id) {
                $invoice = PurchaseInvoice::find($payment->purchase_invoice_id);
                if ($invoice) {
                    $invoice->paid_amount += $payment->amount;
                    if ($invoice->paid_amount >= $invoice->grand_total) {
                        $invoice->status = 'paid';
                    } else {
                        $invoice->status = 'partially_paid';
                    }
                    $invoice->save();
                }
            }

            // Debit AP, Credit Cash/Bank
            if ($payment->amount > 0) {
                $apAccountId = \App\Models\ChartOfAccount::where('code', '2000')->value('id') ?? \App\Models\ChartOfAccount::first()->id;
                $cashAccountId = \App\Models\ChartOfAccount::where('code', '1000')->value('id') ?? \App\Models\ChartOfAccount::first()->id;

                $this->journalService->createAutomaticJournal([
                    'company_id' => $payment->company_id,
                    'date' => $payment->payment_date,
                    'memo' => 'Supplier Payment ' . $payment->payment_number,
                ], [
                    [
                        'chart_of_account_id' => $apAccountId,
                        'description' => 'Accounts Payable Reduced',
                        'debit' => $payment->amount,
                        'credit' => 0,
                    ],
                    [
                        'chart_of_account_id' => $cashAccountId,
                        'description' => 'Cash/Bank Payment',
                        'debit' => 0,
                        'credit' => $payment->amount,
                    ]
                ]);
            }

            return $payment;
        });
    }
}
EOT,
];

foreach ($services as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Created $filename\n";
}
