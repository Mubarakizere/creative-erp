<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransfer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use Illuminate\Support\Facades\DB;
use App\Events\InventoryUpdated;

class InventoryEngine
{
    /**
     * Get or create an inventory record.
     */
    public function getInventory(Product $product, Warehouse $warehouse, $variantId = null, $zoneId = null)
    {
        return Inventory::firstOrCreate([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'product_variant_id' => $variantId,
            'warehouse_zone_id' => $zoneId,
            'company_id' => $product->company_id,
        ]);
    }

    /**
     * Increase stock.
     */
    public function stockIn(Product $product, Warehouse $warehouse, float $quantity, $reason = 'stock_in', $reference = null, $variantId = null, $zoneId = null, $userId = null, $unitCost = null)
    {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $reason, $reference, $variantId, $zoneId, $userId, $unitCost) {
            $inventory = $this->getInventory($product, $warehouse, $variantId, $zoneId);
            
            $inventory->available_quantity += $quantity;
            $inventory->incoming_quantity += $quantity;
            $inventory->save();

            $transaction = $inventory->transactions()->create([
                'type' => $reason,
                'quantity' => $quantity,
                'unit_cost' => $unitCost ?? $product->cost_price,
                'date' => now(),
                'user_id' => $userId ?? auth()->id(),
                'company_id' => $product->company_id,
            ]);

            if ($reference) {
                $transaction->reference()->associate($reference);
                $transaction->save();
            }

            event(new InventoryUpdated($inventory, $transaction));

            return $transaction;
        });
    }

    /**
     * Decrease stock.
     */
    public function stockOut(Product $product, Warehouse $warehouse, float $quantity, $reason = 'stock_out', $reference = null, $variantId = null, $zoneId = null, $userId = null)
    {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $reason, $reference, $variantId, $zoneId, $userId) {
            $inventory = $this->getInventory($product, $warehouse, $variantId, $zoneId);
            
            if (!$product->allow_negative_stock && $inventory->available_quantity < $quantity) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }

            $inventory->available_quantity -= $quantity;
            $inventory->outgoing_quantity += $quantity;
            $inventory->save();

            $transaction = $inventory->transactions()->create([
                'type' => $reason,
                'quantity' => -$quantity,
                'date' => now(),
                'user_id' => $userId ?? auth()->id(),
                'company_id' => $product->company_id,
            ]);

            if ($reference) {
                $transaction->reference()->associate($reference);
                $transaction->save();
            }

            event(new InventoryUpdated($inventory, $transaction));

            return $transaction;
        });
    }

    /**
     * Transfer stock from one warehouse/zone to another.
     */
    public function transfer(InventoryTransfer $transfer, $userId = null)
    {
        if ($transfer->status !== 'approved') {
            throw new \Exception("Transfer must be approved before execution.");
        }

        return DB::transaction(function () use ($transfer, $userId) {
            $items = $transfer->items ?? [];
            $transactions = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $variantId = $item['product_variant_id'] ?? null;

                // Stock Out from Source
                $outTx = $this->stockOut(
                    $product,
                    $transfer->fromWarehouse,
                    $quantity,
                    'transfer_out',
                    $transfer,
                    $variantId,
                    $transfer->from_zone_id,
                    $userId
                );

                // Stock In to Destination
                $inTx = $this->stockIn(
                    $product,
                    $transfer->toWarehouse,
                    $quantity,
                    'transfer_in',
                    $transfer,
                    $variantId,
                    $transfer->to_zone_id,
                    $userId
                );

                $transactions[] = ['out' => $outTx, 'in' => $inTx];
            }

            $transfer->update(['status' => 'completed']);
            return $transactions;
        });
    }

    /**
     * Apply an adjustment.
     */
    public function applyAdjustment(InventoryAdjustment $adjustment, $userId = null)
    {
        if ($adjustment->status !== 'approved') {
            throw new \Exception("Adjustment must be approved before execution.");
        }

        return DB::transaction(function () use ($adjustment, $userId) {
            $items = $adjustment->items ?? [];
            $transactions = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $type = $item['type']; // increase or decrease
                $variantId = $item['product_variant_id'] ?? null;

                if ($type === 'increase') {
                    $transactions[] = $this->stockIn(
                        $product,
                        $adjustment->warehouse,
                        $quantity,
                        'adjustment',
                        $adjustment,
                        $variantId,
                        null,
                        $userId
                    );
                } elseif ($type === 'decrease') {
                    $transactions[] = $this->stockOut(
                        $product,
                        $adjustment->warehouse,
                        $quantity,
                        'adjustment',
                        $adjustment,
                        $variantId,
                        null,
                        $userId
                    );
                }
            }

            $adjustment->update(['status' => 'completed']);
            return $transactions;
        });
    }

    /**
     * Reserve stock for future use (e.g., Quotations, Sales Orders).
     */
    public function reserve(Product $product, Warehouse $warehouse, float $quantity, $reference, $expiresAt = null, $variantId = null, $zoneId = null)
    {
        return DB::transaction(function () use ($product, $warehouse, $quantity, $reference, $expiresAt, $variantId, $zoneId) {
            $inventory = $this->getInventory($product, $warehouse, $variantId, $zoneId);

            if (!$product->allow_negative_stock && $inventory->available_quantity < $quantity) {
                throw new \Exception("Insufficient available stock to reserve.");
            }

            $inventory->available_quantity -= $quantity;
            $inventory->reserved_quantity += $quantity;
            $inventory->save();

            $reservation = $product->inventoryReservations()->create([
                'quantity' => $quantity,
                'expires_at' => $expiresAt,
                'status' => 'active',
                'company_id' => $product->company_id,
                'warehouse_id' => $warehouse->id,
                'zone_id' => $zoneId,
            ]);
            $reservation->reference()->associate($reference);
            $reservation->save();

            $inventory->transactions()->create([
                'type' => 'reservation',
                'quantity' => -$quantity, // It removes from available
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => $product->company_id,
            ]);

            return $reservation;
        });
    }

    /**
     * Release reserved stock.
     */
    public function releaseReservation(\App\Models\InventoryReservation $reservation)
    {
        if ($reservation->status !== 'active') {
            return;
        }

        return DB::transaction(function () use ($reservation) {
            $inventory = Inventory::where('product_id', $reservation->product_id)
                ->where('warehouse_id', $reservation->warehouse_id)
                ->where('warehouse_zone_id', $reservation->zone_id)
                ->first();

            if ($inventory) {
                $inventory->reserved_quantity -= $reservation->quantity;
                if ($inventory->reserved_quantity < 0) $inventory->reserved_quantity = 0;
                $inventory->available_quantity += $reservation->quantity;
                $inventory->save();

                $inventory->transactions()->create([
                    'type' => 'release',
                    'quantity' => $reservation->quantity,
                    'date' => now(),
                    'user_id' => auth()->id(),
                    'company_id' => $reservation->company_id,
                ]);
            }

            $reservation->update(['status' => 'released']);
        });
    }
}
