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
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'reason' => $data['reason'] ?? null,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Approve and execute the movement.
     */
    public function executeMovement(WarehouseMovement $movement, string $userId): void
    {
        DB::transaction(function () use ($movement, $userId) {
            $productId = $movement->product_id;
            $quantity = $movement->quantity;

            // Deduct from source
            $sourceQuery = Inventory::where('company_id', $movement->company_id)
                ->where('warehouse_id', $movement->source_warehouse_id)
                ->where('product_id', $productId);

            if ($movement->source_bin_id) {
                $sourceQuery->where('warehouse_bin_id', $movement->source_bin_id);
            } elseif ($movement->source_zone_id) {
                $sourceQuery->where('warehouse_zone_id', $movement->source_zone_id);
            }

            $sourceInventory = $sourceQuery->firstOrFail();

            if ($sourceInventory->available_quantity < $quantity) {
                throw new \RuntimeException("Insufficient stock. Available: {$sourceInventory->available_quantity}, Requested: {$quantity}");
            }

            $sourceInventory->decrement('available_quantity', $quantity);

            if ($movement->source_bin_id) {
                WarehouseBin::where('id', $movement->source_bin_id)->decrement('current_quantity', $quantity);
            }

            // Add to destination
            $destAttributes = [
                'company_id' => $movement->company_id,
                'warehouse_id' => $movement->destination_warehouse_id,
                'product_id' => $productId,
            ];

            if ($movement->destination_bin_id) {
                $destAttributes['warehouse_bin_id'] = $movement->destination_bin_id;
                $destAttributes['warehouse_zone_id'] = $movement->destination_zone_id;
            } elseif ($movement->destination_zone_id) {
                $destAttributes['warehouse_zone_id'] = $movement->destination_zone_id;
                $destAttributes['warehouse_bin_id'] = null;
            } else {
                $destAttributes['warehouse_zone_id'] = null;
                $destAttributes['warehouse_bin_id'] = null;
            }

            $destInventory = Inventory::firstOrCreate($destAttributes, [
                'available_quantity' => 0,
            ]);

            $destInventory->increment('available_quantity', $quantity);

            if ($movement->destination_bin_id) {
                WarehouseBin::where('id', $movement->destination_bin_id)->increment('current_quantity', $quantity);
            }

            // History - transfer_out
            InventoryTransaction::create([
                'company_id' => $movement->company_id,
                'inventory_id' => $sourceInventory->id,
                'type' => 'transfer_out',
                'quantity' => -$quantity,
                'unit_cost' => 0,
                'reference_type' => WarehouseMovement::class,
                'reference_id' => $movement->id,
                'date' => now(),
                'created_by' => $userId,
            ]);

            // History - transfer_in
            InventoryTransaction::create([
                'company_id' => $movement->company_id,
                'inventory_id' => $destInventory->id,
                'type' => 'transfer_in',
                'quantity' => $quantity,
                'unit_cost' => 0,
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
