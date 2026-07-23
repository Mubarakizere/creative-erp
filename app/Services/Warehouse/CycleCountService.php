<?php

namespace App\Services\Warehouse;

use App\Models\Inventory;
use App\Models\WarehouseCycleCount;
use App\Models\StockCount;
use App\Models\StockCountItem;
use Illuminate\Support\Facades\DB;
use App\Services\Finance\AccountingEngine;
use App\Models\InventoryAdjustment;

class CycleCountService
{
    /**
     * Initiate a cycle count.
     */
    public function initiateCount(array $data, string $userId): WarehouseCycleCount
    {
        return DB::transaction(function () use ($data, $userId) {
            // Integrate with Inventory module's StockCount
            $stockCount = StockCount::create([
                'company_id' => $data['company_id'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => 'pending',
                'created_by' => $userId,
                'date' => now(),
            ]);

            return WarehouseCycleCount::create([
                'company_id' => $data['company_id'],
                'warehouse_id' => $data['warehouse_id'],
                'stock_count_id' => $stockCount->id,
                'count_number' => 'CC-' . strtoupper(uniqid()),
                'type' => $data['type'], // daily, weekly, monthly, abc
                'status' => 'pending',
                'assigned_to' => $data['assigned_to'] ?? null,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Record counts and detect variances.
     */
    public function recordCount(WarehouseCycleCount $cycleCount, array $items, string $userId): void
    {
        DB::transaction(function () use ($cycleCount, $items, $userId) {
            $varianceDetected = false;

            foreach ($items as $item) {
                $inventory = Inventory::findOrFail($item['inventory_id']);
                $variance = $item['counted_quantity'] - $inventory->quantity;

                StockCountItem::create([
                    'stock_count_id' => $cycleCount->stock_count_id,
                    'inventory_id' => $inventory->id,
                    'expected_quantity' => $inventory->quantity,
                    'counted_quantity' => $item['counted_quantity'],
                    'variance' => $variance,
                ]);

                if ($variance != 0) {
                    $varianceDetected = true;
                }
            }

            $cycleCount->update([
                'status' => $varianceDetected ? 'variance_detected' : 'completed',
            ]);

            \App\Models\WarehouseAudit::create([
                'company_id' => $cycleCount->company_id,
                'warehouse_id' => $cycleCount->warehouse_id,
                'action' => 'cycle_count_recorded',
                'auditable_type' => WarehouseCycleCount::class,
                'auditable_id' => $cycleCount->id,
                'details' => json_encode(['variance_detected' => $varianceDetected]),
                'user_id' => $userId,
            ]);
        });
    }

    /**
     * Approve variance and adjust inventory.
     */
    public function approveVariance(WarehouseCycleCount $cycleCount, string $userId): void
    {
        DB::transaction(function () use ($cycleCount, $userId) {
            $stockCount = $cycleCount->stockCount;
            
            foreach ($stockCount->items as $item) {
                if ($item->variance != 0) {
                    $inventory = $item->inventory;
                    
                    // Create Adjustment
                    InventoryAdjustment::create([
                        'company_id' => $cycleCount->company_id,
                        'inventory_id' => $inventory->id,
                        'type' => $item->variance > 0 ? 'addition' : 'deduction',
                        'quantity' => abs($item->variance),
                        'reason' => 'Cycle count variance approved: ' . $cycleCount->count_number,
                        'date' => now(),
                        'created_by' => $userId,
                    ]);

                    // Update Inventory
                    $inventory->update(['quantity' => $item->counted_quantity]);

                    // Also update bin if applicable
                    if ($inventory->warehouse_bin_id) {
                        $bin = \App\Models\WarehouseBin::find($inventory->warehouse_bin_id);
                        if ($bin) {
                            $bin->increment('current_quantity', $item->variance);
                        }
                    }

                    // Accounting integration
                    app(AccountingEngine::class)->recordInventoryAdjustment($inventory, $item->variance);
                }
            }

            $cycleCount->update([
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            $stockCount->update(['status' => 'completed']);

            \App\Models\WarehouseAudit::create([
                'company_id' => $cycleCount->company_id,
                'warehouse_id' => $cycleCount->warehouse_id,
                'action' => 'cycle_count_approved',
                'auditable_type' => WarehouseCycleCount::class,
                'auditable_id' => $cycleCount->id,
                'user_id' => $userId,
            ]);
        });
    }
}
