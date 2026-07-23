<?php

namespace App\Services\Warehouse;

use App\Models\Inventory;
use App\Models\WarehouseBin;
use App\Models\WarehouseReturn;
use Illuminate\Support\Facades\DB;
use App\Services\Finance\AccountingEngine;
use App\Models\InventoryTransaction;

class WarehouseReturnService
{
    /**
     * Log a warehouse return.
     */
    public function logReturn(array $data, string $userId): WarehouseReturn
    {
        return DB::transaction(function () use ($data, $userId) {
            return WarehouseReturn::create([
                'company_id' => $data['company_id'],
                'warehouse_id' => $data['warehouse_id'],
                'return_number' => 'RET-' . strtoupper(uniqid()),
                'type' => $data['type'], // customer_return, supplier_return, damaged_stock
                'status' => 'pending',
                'returnable_type' => $data['returnable_type'] ?? null,
                'returnable_id' => $data['returnable_id'] ?? null,
                'requires_accounting_adjustment' => $data['requires_accounting_adjustment'] ?? false,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Inspect and process return.
     */
    public function inspectReturn(WarehouseReturn $return, array $inspectionData, string $userId): void
    {
        DB::transaction(function () use ($return, $inspectionData, $userId) {
            $return->update([
                'status' => $inspectionData['status'], // restocked, disposed
                'inspected_by' => $userId,
                'inspected_at' => now(),
                'inspection_notes' => $inspectionData['notes'] ?? null,
            ]);

            if ($inspectionData['status'] === 'restocked') {
                $this->restockItems($return, $inspectionData['items'], $userId);
            } elseif ($inspectionData['status'] === 'disposed' && $return->requires_accounting_adjustment) {
                // Adjust accounting for inventory loss/damage
                app(AccountingEngine::class)->recordInventoryWriteOff($return, $inspectionData['loss_amount']);
            }

            \App\Models\WarehouseAudit::create([
                'company_id' => $return->company_id,
                'warehouse_id' => $return->warehouse_id,
                'action' => 'return_inspected',
                'auditable_type' => WarehouseReturn::class,
                'auditable_id' => $return->id,
                'user_id' => $userId,
            ]);
        });
    }

    private function restockItems(WarehouseReturn $return, array $items, string $userId): void
    {
        foreach ($items as $item) {
            $bin = WarehouseBin::findOrFail($item['bin_id']);
            $bin->increment('current_quantity', $item['quantity']);

            $inventory = Inventory::firstOrCreate(
                [
                    'company_id' => $return->company_id,
                    'warehouse_id' => $return->warehouse_id,
                    'warehouse_zone_id' => $bin->warehouse_zone_id,
                    'warehouse_bin_id' => $bin->id,
                    'product_id' => $item['product_id'],
                ],
                [
                    'quantity' => 0,
                    'valuation_method' => 'FIFO',
                    'unit_cost' => $item['unit_cost'] ?? 0,
                ]
            );

            $inventory->increment('quantity', $item['quantity']);

            InventoryTransaction::create([
                'company_id' => $return->company_id,
                'inventory_id' => $inventory->id,
                'type' => 'return_restock',
                'quantity' => $item['quantity'],
                'unit_cost' => $inventory->unit_cost,
                'total_cost' => $inventory->unit_cost * $item['quantity'],
                'reference_type' => WarehouseReturn::class,
                'reference_id' => $return->id,
                'date' => now(),
                'created_by' => $userId,
            ]);
        }
    }
}
