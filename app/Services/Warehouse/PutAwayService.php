<?php

namespace App\Services\Warehouse;

use App\Models\Inventory;
use App\Models\WarehouseBin;
use App\Models\WarehouseTask;
use App\Models\GoodsReceiptItem;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryTransaction;

class PutAwayService
{
    /**
     * Generate put away tasks for goods receipt items.
     */
    public function generateTasksForReceipt(string $companyId, string $warehouseId, iterable $receiptItems): void
    {
        DB::transaction(function () use ($companyId, $warehouseId, $receiptItems) {
            foreach ($receiptItems as $item) {
                // Determine best bin
                $bin = $this->suggestBin($companyId, $warehouseId, $item->product_id, $item->quantity_received);

                if ($bin) {
                    WarehouseTask::create([
                        'company_id' => $companyId,
                        'warehouse_id' => $warehouseId,
                        'type' => 'put_away',
                        'status' => 'pending',
                        'taskable_type' => GoodsReceiptItem::class,
                        'taskable_id' => $item->id,
                        'priority' => 1,
                        'notes' => 'Auto-assigned to Bin ' . $bin->code,
                    ]);
                } else {
                    // Create unassigned task or overflow
                    WarehouseTask::create([
                        'company_id' => $companyId,
                        'warehouse_id' => $warehouseId,
                        'type' => 'put_away',
                        'status' => 'pending',
                        'taskable_type' => GoodsReceiptItem::class,
                        'taskable_id' => $item->id,
                        'priority' => 1,
                        'notes' => 'Requires manual bin assignment',
                    ]);
                }
            }
        });
    }

    /**
     * Suggest a bin for a given product and quantity.
     */
    public function suggestBin(string $companyId, string $warehouseId, string $productId, float $quantity): ?WarehouseBin
    {
        // Find existing bin with same product
        $existingInventoryBin = Inventory::where('company_id', $companyId)
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->whereNotNull('warehouse_bin_id')
            ->first();

        if ($existingInventoryBin) {
            $bin = WarehouseBin::find($existingInventoryBin->warehouse_bin_id);
            if ($bin && $this->hasCapacity($bin, $quantity)) {
                return $bin;
            }
        }

        // Find empty bin in active status
        $emptyBin = WarehouseBin::where('company_id', $companyId)
            ->whereHas('zone', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->where('status', 'active')
            ->where('current_quantity', 0)
            ->first();

        if ($emptyBin && $this->hasCapacity($emptyBin, $quantity)) {
            return $emptyBin;
        }

        return null;
    }

    /**
     * Execute a put away task
     */
    public function executePutAway(WarehouseTask $task, string $binId, string $userId): void
    {
        DB::transaction(function () use ($task, $binId, $userId) {
            $bin = WarehouseBin::findOrFail($binId);
            $receiptItem = $task->taskable;

            // Update Bin quantity
            $bin->increment('current_quantity', $receiptItem->quantity_received);
            
            // Deduct from generic warehouse inventory (created by GoodsReceiptService)
            $genericInventory = Inventory::where([
                'company_id' => $task->company_id,
                'warehouse_id' => $task->warehouse_id,
                'product_id' => $receiptItem->product_id,
            ])->whereNull('warehouse_zone_id')->whereNull('warehouse_bin_id')->first();

            if ($genericInventory && $genericInventory->available_quantity >= $receiptItem->quantity_received) {
                $genericInventory->decrement('available_quantity', $receiptItem->quantity_received);
                if ($genericInventory->incoming_quantity >= $receiptItem->quantity_received) {
                    $genericInventory->decrement('incoming_quantity', $receiptItem->quantity_received);
                }
            }
            
            // Generate/Update Bin-level Inventory entry
            $inventory = Inventory::firstOrCreate(
                [
                    'company_id' => $task->company_id,
                    'warehouse_id' => $task->warehouse_id,
                    'warehouse_zone_id' => $bin->warehouse_zone_id,
                    'warehouse_bin_id' => $bin->id,
                    'product_id' => $receiptItem->product_id,
                ],
                [
                    'available_quantity' => 0,
                    'created_by' => $userId,
                ]
            );

            $inventory->increment('available_quantity', $receiptItem->quantity_received);

            // Create transaction history
            InventoryTransaction::create([
                'company_id' => $task->company_id,
                'inventory_id' => $inventory->id,
                'type' => 'receipt', // Or maybe 'put_away' to avoid confusion with goods_receipt
                'quantity' => $receiptItem->quantity_received,
                'unit_cost' => $receiptItem->unit_price ?? 0,
                'date' => now(),
                'user_id' => $userId,
            ]);

            // Update task status
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Audit log
            \App\Models\WarehouseAudit::create([
                'company_id' => $task->company_id,
                'warehouse_id' => $task->warehouse_id,
                'action' => 'put_away',
                'auditable_type' => WarehouseTask::class,
                'auditable_id' => $task->id,
                'details' => json_encode(['bin' => $bin->code, 'quantity' => $receiptItem->quantity_received]),
                'user_id' => $userId,
            ]);
        });
    }

    private function hasCapacity(WarehouseBin $bin, float $quantity): bool
    {
        if (!$bin->capacity) return true;
        return ($bin->current_quantity + $quantity) <= $bin->capacity;
    }
}
