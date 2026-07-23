<?php

namespace App\Services\Warehouse;

use App\Models\WarehousePacking;
use App\Models\WarehousePicking;
use App\Models\WarehouseShipment;
use Illuminate\Support\Facades\DB;

class PackingService
{
    /**
     * Create a packing record from a completed picking.
     */
    public function createPackingFromPicking(WarehousePicking $picking, string $userId): WarehousePacking
    {
        return DB::transaction(function () use ($picking, $userId) {
            $packing = WarehousePacking::create([
                'company_id' => $picking->company_id,
                'warehouse_id' => $picking->warehouse_id,
                'warehouse_picking_id' => $picking->id,
                'packing_number' => 'PACK-' . strtoupper(uniqid()),
                'status' => 'pending',
                'created_by' => $userId,
            ]);

            return $packing;
        });
    }

    /**
     * Complete a packing process.
     */
    public function completePacking(WarehousePacking $packing, array $data, string $userId): void
    {
        DB::transaction(function () use ($packing, $data, $userId) {
            $packing->update([
                'status' => 'completed',
                'total_weight' => $data['total_weight'] ?? 0,
                'length' => $data['length'] ?? null,
                'width' => $data['width'] ?? null,
                'height' => $data['height'] ?? null,
                'notes' => $data['notes'] ?? null,
                'packed_by' => $userId,
            ]);

            \App\Models\WarehouseAudit::create([
                'company_id' => $packing->company_id,
                'warehouse_id' => $packing->warehouse_id,
                'action' => 'packing_completed',
                'auditable_type' => WarehousePacking::class,
                'auditable_id' => $packing->id,
                'user_id' => $userId,
            ]);
        });
    }
}
