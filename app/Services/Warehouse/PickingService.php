<?php

namespace App\Services\Warehouse;

use App\Models\Inventory;
use App\Models\WarehouseBin;
use App\Models\WarehousePicking;
use App\Models\WarehouseTask;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryTransaction;

class PickingService
{
    /**
     * Generate a picking request for a sales order or transfer.
     */
    public function generatePicking(string $companyId, string $warehouseId, $pickableModel, string $type = 'standard'): WarehousePicking
    {
        return DB::transaction(function () use ($companyId, $warehouseId, $pickableModel, $type) {
            $picking = WarehousePicking::create([
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId,
                'picking_number' => 'PICK-' . strtoupper(uniqid()),
                'type' => $type,
                'status' => 'pending',
                'pickable_type' => get_class($pickableModel),
                'pickable_id' => $pickableModel->id,
            ]);

            // Assuming $pickableModel has items relation
            foreach ($pickableModel->items as $item) {
                // Determine bins with enough quantity
                $this->allocateBinsForPicking($picking, $item);
            }

            return $picking;
        });
    }

    /**
     * Allocate bins for picking a specific item.
     */
    private function allocateBinsForPicking(WarehousePicking $picking, $item): void
    {
        $remainingQuantity = $item->quantity;

        $inventories = Inventory::where('company_id', $picking->company_id)
            ->where('warehouse_id', $picking->warehouse_id)
            ->where('product_id', $item->product_id)
            ->where('quantity', '>', 0)
            ->whereNotNull('warehouse_bin_id')
            ->orderBy('quantity', 'desc')
            ->get();

        foreach ($inventories as $inventory) {
            if ($remainingQuantity <= 0) break;

            $allocateQty = min($remainingQuantity, $inventory->quantity);
            $remainingQuantity -= $allocateQty;

            WarehouseTask::create([
                'company_id' => $picking->company_id,
                'warehouse_id' => $picking->warehouse_id,
                'type' => 'picking',
                'status' => 'pending',
                'taskable_type' => WarehousePicking::class,
                'taskable_id' => $picking->id,
                'priority' => 2,
                'notes' => json_encode([
                    'product_id' => $item->product_id,
                    'quantity' => $allocateQty,
                    'bin_id' => $inventory->warehouse_bin_id
                ]),
            ]);
        }
    }

    /**
     * Complete a picking task.
     */
    public function completePickTask(WarehouseTask $task, string $userId): void
    {
        DB::transaction(function () use ($task, $userId) {
            $data = json_decode($task->notes, true);
            $bin = WarehouseBin::findOrFail($data['bin_id']);
            $quantity = $data['quantity'];

            // Deduct from bin
            $bin->decrement('current_quantity', $quantity);

            // Deduct from inventory
            $inventory = Inventory::where('company_id', $task->company_id)
                ->where('warehouse_id', $task->warehouse_id)
                ->where('warehouse_bin_id', $bin->id)
                ->where('product_id', $data['product_id'])
                ->firstOrFail();

            $inventory->decrement('quantity', $quantity);

            // History
            InventoryTransaction::create([
                'company_id' => $task->company_id,
                'inventory_id' => $inventory->id,
                'type' => 'picking',
                'quantity' => -$quantity,
                'unit_cost' => $inventory->unit_cost,
                'total_cost' => -($inventory->unit_cost * $quantity),
                'reference_type' => WarehousePicking::class,
                'reference_id' => $task->taskable_id,
                'date' => now(),
                'created_by' => $userId,
            ]);

            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            \App\Models\WarehouseAudit::create([
                'company_id' => $task->company_id,
                'warehouse_id' => $task->warehouse_id,
                'action' => 'picking',
                'auditable_type' => WarehouseTask::class,
                'auditable_id' => $task->id,
                'details' => json_encode(['bin' => $bin->code, 'quantity' => $quantity]),
                'user_id' => $userId,
            ]);

            $this->checkPickingStatus($task->taskable);
        });
    }

    private function checkPickingStatus(WarehousePicking $picking): void
    {
        $pendingTasks = WarehouseTask::where('taskable_type', WarehousePicking::class)
            ->where('taskable_id', $picking->id)
            ->where('status', '!=', 'completed')
            ->count();

        if ($pendingTasks === 0) {
            $picking->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        } else {
            $picking->update(['status' => 'picking']);
        }
    }
}
