<?php

namespace App\Services\Warehouse;

use App\Models\Inventory;
use App\Models\WarehouseBin;
use App\Models\WarehouseMovement;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryTransaction;

class WarehouseMovementService
{
    /**
     * Request a warehouse movement.
     */
    public function requestMovement(array $data, string $userId): WarehouseMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            return WarehouseMovement::create([
                'company_id' => $data['company_id'],
                'movement_number' => 'MOV-' . strtoupper(uniqid()),
                'type' => $data['type'], // bin_to_bin, zone_to_zone, warehouse_to_warehouse
                'status' => 'pending',
                'source_warehouse_id' => $data['source_warehouse_id'],
                'source_zone_id' => $data['source_zone_id'] ?? null,
                'source_bin_id' => $data['source_bin_id'] ?? null,
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'destination_zone_id' => $data['destination_zone_id'] ?? null,
                'destination_bin_id' => $data['destination_bin_id'] ?? null,
                'reason' => $data['reason'] ?? null,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Approve and execute the movement.
     */
    public function executeMovement(WarehouseMovement $movement, string $productId, float $quantity, string $userId): void
    {
        DB::transaction(function () use ($movement, $productId, $quantity, $userId) {
            // Deduct from source
            $sourceInventory = Inventory::where('company_id', $movement->company_id)
                ->where('warehouse_id', $movement->source_warehouse_id)
                ->where('warehouse_bin_id', $movement->source_bin_id)
                ->where('product_id', $productId)
                ->firstOrFail();

            $sourceInventory->decrement('quantity', $quantity);

            if ($movement->source_bin_id) {
                WarehouseBin::where('id', $movement->source_bin_id)->decrement('current_quantity', $quantity);
            }

            // Add to destination
            $destInventory = Inventory::firstOrCreate(
                [
                    'company_id' => $movement->company_id,
                    'warehouse_id' => $movement->destination_warehouse_id,
                    'warehouse_zone_id' => $movement->destination_zone_id,
                    'warehouse_bin_id' => $movement->destination_bin_id,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => 0,
                    'valuation_method' => $sourceInventory->valuation_method,
                    'unit_cost' => $sourceInventory->unit_cost,
                ]
            );

            $destInventory->increment('quantity', $quantity);

            if ($movement->destination_bin_id) {
                WarehouseBin::where('id', $movement->destination_bin_id)->increment('current_quantity', $quantity);
            }

            // History
            InventoryTransaction::create([
                'company_id' => $movement->company_id,
                'inventory_id' => $sourceInventory->id,
                'type' => 'transfer_out',
                'quantity' => -$quantity,
                'unit_cost' => $sourceInventory->unit_cost,
                'total_cost' => -($sourceInventory->unit_cost * $quantity),
                'reference_type' => WarehouseMovement::class,
                'reference_id' => $movement->id,
                'date' => now(),
                'created_by' => $userId,
            ]);

            InventoryTransaction::create([
                'company_id' => $movement->company_id,
                'inventory_id' => $destInventory->id,
                'type' => 'transfer_in',
                'quantity' => $quantity,
                'unit_cost' => $sourceInventory->unit_cost,
                'total_cost' => ($sourceInventory->unit_cost * $quantity),
                'reference_type' => WarehouseMovement::class,
                'reference_id' => $movement->id,
                'date' => now(),
                'created_by' => $userId,
            ]);

            $movement->update([
                'status' => 'completed',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            \App\Models\WarehouseAudit::create([
                'company_id' => $movement->company_id,
                'warehouse_id' => $movement->source_warehouse_id,
                'action' => 'movement_executed',
                'auditable_type' => WarehouseMovement::class,
                'auditable_id' => $movement->id,
                'user_id' => $userId,
            ]);
        });
    }
}
