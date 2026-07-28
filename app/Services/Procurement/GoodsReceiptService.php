<?php
namespace App\Services\Procurement;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\Inventory\InventoryEngine;
use App\Services\Finance\JournalService;
use App\Services\Warehouse\PutAwayService;
use Illuminate\Support\Facades\DB;

class GoodsReceiptService
{
    protected InventoryEngine $inventoryEngine;
    protected JournalService $journalService;
    protected PutAwayService $putAwayService;

    public function __construct(InventoryEngine $inventoryEngine, JournalService $journalService, PutAwayService $putAwayService)
    {
        $this->inventoryEngine = $inventoryEngine;
        $this->journalService = $journalService;
        $this->putAwayService = $putAwayService;
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
                    $poItem = \App\Models\PurchaseOrderItem::where('id', $grItem->purchase_order_item_id)->lockForUpdate()->first();
                    if ($poItem) {
                        $remaining = max(0, $poItem->quantity - $poItem->received_quantity);
                        if ($grItem->quantity_received > $remaining) {
                            throw new \Exception("Cannot receive more than remaining quantity for product " . $grItem->product?->name);
                        }
                        
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
                $inventoryAccount = \App\Models\ChartOfAccount::where('code', '1200')->first() ?? \App\Models\ChartOfAccount::first();
                $grniAccount = \App\Models\ChartOfAccount::where('code', '2100')->first() ?? \App\Models\ChartOfAccount::first();

                if ($inventoryAccount && $grniAccount) {
                    $this->journalService->createAutomaticJournal([
                        'company_id' => $gr->company_id,
                        'date' => $gr->receipt_date,
                        'memo' => 'Goods Receipt ' . $gr->code,
                    ], [
                        [
                            'chart_of_account_id' => $inventoryAccount->id,
                            'description' => 'Inventory Received',
                            'debit' => $totalCost,
                            'credit' => 0,
                        ],
                        [
                            'chart_of_account_id' => $grniAccount->id,
                            'description' => 'Goods Receipt Not Invoiced',
                            'debit' => 0,
                            'credit' => $totalCost,
                        ]
                    ]);
                }
            }
            
            // Trigger WMS Put Away Tasks
            if ($gr->warehouse_id) {
                $this->putAwayService->generateTasksForReceipt(
                    $gr->company_id,
                    $gr->warehouse_id,
                    $gr->items
                );
            }
            
            return $gr;
        });
    }
}