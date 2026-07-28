<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\InventoryValuation;
use App\Models\InventoryTransaction;

class InventoryValuationService
{
    /**
     * Calculate current stock valuation dynamically.
     */
    public function calculateValuation(Product $product, Warehouse $warehouse = null)
    {
        // If warehouse is not specified, sum up total available quantity across all warehouses.
        $inventories = Inventory::where('product_id', $product->id);
        if ($warehouse) {
            $inventories->where('warehouse_id', $warehouse->id);
        }
        
        $availableQty = $inventories->sum('available_quantity') ?? 0;

        if ($availableQty <= 0) {
            return 0;
        }

        switch ($product->valuation_method) {
            case 'FIFO':
                return $this->calculateFIFO($product, $availableQty);
            case 'Weighted Average':
                return $this->calculateWAC($product, $availableQty);
            case 'Standard Cost':
            default:
                return $this->calculateStandardCost($product, $availableQty);
        }
    }

    protected function calculateStandardCost(Product $product, $availableQty)
    {
        return $availableQty * $product->cost_price;
    }

    protected function calculateWAC(Product $product, $availableQty)
    {
        // Get all inbound transactions to calculate the historical average cost
        $inboundTransactions = $product->inventoryTransactions()
            ->where('quantity', '>', 0)
            ->get();

        $totalQty = $inboundTransactions->sum('quantity');
        $totalCost = $inboundTransactions->sum(function ($transaction) {
            return $transaction->quantity * ($transaction->unit_cost ?? 0);
        });

        if ($totalQty == 0) {
            return $this->calculateStandardCost($product, $availableQty);
        }

        $averageCost = $totalCost / $totalQty;
        return $availableQty * $averageCost;
    }

    protected function calculateFIFO(Product $product, $availableQty)
    {
        // Fetch inbound transactions, newest first (LIFO order, because we consume oldest first, 
        // so whatever is currently left in stock must be the NEWEST layers).
        $inboundLayers = $product->inventoryTransactions()
            ->where('quantity', '>', 0)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $remainingQtyToValue = $availableQty;
        $totalValue = 0;

        foreach ($inboundLayers as $layer) {
            if ($remainingQtyToValue <= 0) {
                break;
            }

            $qtyToTake = min($layer->quantity, $remainingQtyToValue);
            $totalValue += $qtyToTake * ($layer->unit_cost ?? $product->cost_price);
            $remainingQtyToValue -= $qtyToTake;
        }

        // If we still have un-valued quantity (e.g. starting balance without transactions), value at standard cost
        if ($remainingQtyToValue > 0) {
            $totalValue += $remainingQtyToValue * $product->cost_price;
        }

        return $totalValue;
    }

    /**
     * Calculate the cost for a specific quantity being issued.
     */
    public function calculateIssueCost(Product $product, float $quantity, Warehouse $warehouse = null)
    {
        if ($quantity <= 0) {
            return 0;
        }

        switch ($product->valuation_method) {
            case 'FIFO':
                return $this->calculateIssueFIFO($product, $quantity, $warehouse);
            case 'Weighted Average':
                // WAC cost is simply the average of current stock * quantity
                // calculateValuation gives total value of current stock, so we divide by availableQty
                $inventories = Inventory::where('product_id', $product->id);
                if ($warehouse) {
                    $inventories->where('warehouse_id', $warehouse->id);
                }
                $availableQty = $inventories->sum('available_quantity') ?? 0;
                
                if ($availableQty > 0) {
                    $totalValue = $this->calculateWAC($product, $availableQty);
                    $unitCost = $totalValue / $availableQty;
                    return $quantity * $unitCost;
                }
                return $this->calculateStandardCost($product, $quantity);
            case 'Standard Cost':
            default:
                return $this->calculateStandardCost($product, $quantity);
        }
    }

    protected function calculateIssueFIFO(Product $product, float $quantity, Warehouse $warehouse = null)
    {
        // For issuing stock, we consume the OLDEST inbound transactions first
        // Unlike valuation which leaves the NEWEST in stock, issuing removes the OLDEST.
        $query = $product->inventoryTransactions()
            ->where('quantity', '>', 0)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');
            
        // Note: Currently, transactions might not be strictly warehouse scoped if they move around,
        // but typically FIFO is tracked globally or per warehouse. We will keep it global for consistency 
        // with calculateFIFO which also does not scope by warehouse.
        
        $inboundLayers = $query->get();
        
        // We need to know how much of the oldest stock has ALREADY been issued 
        // to find the current active layer.
        $totalIssuedSoFarQuery = $product->inventoryTransactions()
            ->where('quantity', '<', 0);
            
        $totalIssuedSoFar = abs($totalIssuedSoFarQuery->sum('quantity') ?? 0);
        
        $remainingQtyToValue = $quantity;
        $totalValue = 0;
        
        foreach ($inboundLayers as $layer) {
            if ($remainingQtyToValue <= 0) {
                break;
            }
            
            // Deduct previously issued quantities from this layer to see what's left in it
            $availableInLayer = max(0, $layer->quantity - $totalIssuedSoFar);
            $totalIssuedSoFar -= min($layer->quantity, $totalIssuedSoFar);
            
            if ($availableInLayer > 0) {
                $qtyToTake = min($availableInLayer, $remainingQtyToValue);
                $totalValue += $qtyToTake * ($layer->unit_cost ?? $product->cost_price);
                $remainingQtyToValue -= $qtyToTake;
            }
        }
        
        // If we still have un-valued quantity (e.g. starting balance), value at standard cost
        if ($remainingQtyToValue > 0) {
            $totalValue += $remainingQtyToValue * $product->cost_price;
        }
        
        return $totalValue;
    }
}
