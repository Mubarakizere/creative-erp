<?php

namespace App\Services\Warehouse;

use App\Models\WarehousePacking;
use App\Models\WarehouseShipment;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class ShippingService
{
    /**
     * Create a shipment for a set of packings.
     */
    public function createShipment(string $companyId, string $warehouseId, array $packingIds, string $userId): WarehouseShipment
    {
        return DB::transaction(function () use ($companyId, $warehouseId, $packingIds, $userId) {
            $shipment = WarehouseShipment::create([
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId,
                'shipment_number' => 'SHIP-' . strtoupper(uniqid()),
                'status' => 'pending',
                'created_by' => $userId,
            ]);

            WarehousePacking::whereIn('id', $packingIds)->update([
                'warehouse_shipment_id' => $shipment->id,
            ]);

            return $shipment;
        });
    }

    /**
     * Dispatch the shipment.
     */
    public function dispatchShipment(WarehouseShipment $shipment, array $data, string $userId): void
    {
        DB::transaction(function () use ($shipment, $data, $userId) {
            $shipment->update([
                'status' => 'shipped',
                'carrier' => $data['carrier'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'shipping_notes' => $data['shipping_notes'] ?? null,
                'shipped_at' => now(),
            ]);

            \App\Models\WarehouseAudit::create([
                'company_id' => $shipment->company_id,
                'warehouse_id' => $shipment->warehouse_id,
                'action' => 'shipment_dispatched',
                'auditable_type' => WarehouseShipment::class,
                'auditable_id' => $shipment->id,
                'user_id' => $userId,
            ]);

            // Notifications can be triggered here
            // app(NotificationService::class)->notifyShipmentDispatched($shipment);
        });
    }

    /**
     * Mark shipment as delivered.
     */
    public function markAsDelivered(WarehouseShipment $shipment, string $userId): void
    {
        $shipment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        \App\Models\WarehouseAudit::create([
            'company_id' => $shipment->company_id,
            'warehouse_id' => $shipment->warehouse_id,
            'action' => 'shipment_delivered',
            'auditable_type' => WarehouseShipment::class,
            'auditable_id' => $shipment->id,
            'user_id' => $userId,
        ]);
    }
}
