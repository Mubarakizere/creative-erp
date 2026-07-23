<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use App\Models\InventoryAdjustment;
use App\Services\Inventory\InventoryValuationService;
use App\Exports\InventoryReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    public function index()
    {
        $reports = [
            'valuation' => 'Inventory Valuation',
            'stock-on-hand' => 'Stock on Hand',
            'low-stock' => 'Low Stock',
            'out-of-stock' => 'Out of Stock',
            'aging' => 'Inventory Aging',
            'warehouse-summary' => 'Warehouse Summary',
            'transactions' => 'Inventory Transactions',
            'adjustments' => 'Adjustment Report',
            'profitability' => 'Product Profitability',
        ];

        return view('admin.inventory.reports.index', compact('reports'));
    }

    public function show($type, Request $request)
    {
        $data = $this->getReportData($type);
        
        if (!$data) {
            abort(404);
        }

        $title = $data['title'];
        $headers = $data['headers'];
        $rows = $data['rows'];

        if ($request->has('export')) {
            $format = $request->get('export');
            return $this->export($format, $title, $headers, $rows);
        }

        return view('admin.inventory.reports.show', compact('title', 'headers', 'rows', 'type'));
    }

    private function export($format, $title, $headers, $rows)
    {
        $fileName = strtolower(str_replace(' ', '_', $title)) . '_' . date('Y_m_d');

        if ($format === 'excel') {
            return Excel::download(new InventoryReportExport($rows, $headers), $fileName . '.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new InventoryReportExport($rows, $headers), $fileName . '.csv');
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.inventory.reports.pdf', compact('title', 'headers', 'rows'));
            return $pdf->download($fileName . '.pdf');
        }

        abort(400, 'Invalid export format');
    }

    private function getReportData($type)
    {
        $companyId = session('company_id') ?? 1;

        switch ($type) {
            case 'valuation':
                return $this->getValuationData($companyId);
            case 'stock-on-hand':
                return $this->getStockOnHandData($companyId);
            case 'low-stock':
                return $this->getLowStockData($companyId);
            case 'out-of-stock':
                return $this->getOutOfStockData($companyId);
            case 'aging':
                return $this->getAgingData($companyId);
            case 'warehouse-summary':
                return $this->getWarehouseSummaryData($companyId);
            case 'transactions':
                return $this->getTransactionsData($companyId);
            case 'adjustments':
                return $this->getAdjustmentsData($companyId);
            case 'profitability':
                return $this->getProfitabilityData($companyId);
            default:
                return null;
        }
    }

    private function getValuationData($companyId)
    {
        $valuationService = app(InventoryValuationService::class);
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)->with('inventory')->get();
        
        $rows = [];
        $totalValuation = 0;

        foreach ($products as $product) {
            $qty = $product->inventory->sum('available_quantity') ?? 0;
            if ($qty > 0) {
                $value = $valuationService->calculateValuation($product);
                $totalValuation += $value;
                $rows[] = [
                    'SKU' => $product->sku,
                    'Product' => $product->name,
                    'Quantity' => number_format($qty),
                    'Unit Cost' => '$' . number_format($value / $qty, 2),
                    'Total Value' => '$' . number_format($value, 2),
                ];
            }
        }

        // Add total row
        if (count($rows) > 0) {
            $rows[] = ['SKU' => '', 'Product' => 'TOTAL', 'Quantity' => '', 'Unit Cost' => '', 'Total Value' => '$' . number_format($totalValuation, 2)];
        }

        return [
            'title' => 'Inventory Valuation',
            'headers' => ['SKU', 'Product', 'Quantity', 'Unit Cost', 'Total Value'],
            'rows' => $rows
        ];
    }

    private function getStockOnHandData($companyId)
    {
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)
            ->with(['inventory.warehouse'])->get();
        
        $rows = [];

        foreach ($products as $product) {
            foreach ($product->inventory as $inv) {
                if ($inv->available_quantity > 0) {
                    $rows[] = [
                        'Warehouse' => $inv->warehouse->name ?? 'Unknown',
                        'SKU' => $product->sku,
                        'Product' => $product->name,
                        'Quantity' => number_format($inv->available_quantity),
                    ];
                }
            }
        }

        return [
            'title' => 'Stock on Hand',
            'headers' => ['Warehouse', 'SKU', 'Product', 'Quantity'],
            'rows' => $rows
        ];
    }

    private function getLowStockData($companyId)
    {
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)->with('inventory')->get();
        
        $rows = [];

        foreach ($products as $product) {
            $qty = $product->inventory->sum('available_quantity') ?? 0;
            if ($qty > 0 && $qty <= $product->minimum_stock) {
                $rows[] = [
                    'SKU' => $product->sku,
                    'Product' => $product->name,
                    'Current Qty' => number_format($qty),
                    'Min Stock' => number_format($product->minimum_stock),
                    'Deficit' => number_format($product->minimum_stock - $qty),
                ];
            }
        }

        return [
            'title' => 'Low Stock',
            'headers' => ['SKU', 'Product', 'Current Qty', 'Min Stock', 'Deficit'],
            'rows' => $rows
        ];
    }

    private function getOutOfStockData($companyId)
    {
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)->with('inventory')->get();
        
        $rows = [];

        foreach ($products as $product) {
            $qty = $product->inventory->sum('available_quantity') ?? 0;
            if ($qty <= 0) {
                $rows[] = [
                    'SKU' => $product->sku,
                    'Product' => $product->name,
                    'Min Stock' => number_format($product->minimum_stock),
                ];
            }
        }

        return [
            'title' => 'Out of Stock',
            'headers' => ['SKU', 'Product', 'Min Stock'],
            'rows' => $rows
        ];
    }

    private function getAgingData($companyId)
    {
        // Approximating aging by finding the oldest stock_in transaction for items we currently hold
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)->with('inventory')->get();
        
        $rows = [];
        $now = now();

        foreach ($products as $product) {
            $qty = $product->inventory->sum('available_quantity') ?? 0;
            if ($qty > 0) {
                $oldestStockIn = InventoryTransaction::whereHas('inventory', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->whereIn('type', ['stock_in', 'adjustment_gain'])->orderBy('date', 'asc')->first();

                if ($oldestStockIn) {
                    $days = $now->diffInDays($oldestStockIn->date);
                    $rows[] = [
                        'SKU' => $product->sku,
                        'Product' => $product->name,
                        'Current Qty' => number_format($qty),
                        'Oldest Stock Date' => $oldestStockIn->date->format('Y-m-d'),
                        'Age (Days)' => $days,
                    ];
                }
            }
        }

        // Sort by oldest first
        usort($rows, function($a, $b) {
            return $b['Age (Days)'] <=> $a['Age (Days)'];
        });

        return [
            'title' => 'Inventory Aging',
            'headers' => ['SKU', 'Product', 'Current Qty', 'Oldest Stock Date', 'Age (Days)'],
            'rows' => $rows
        ];
    }

    private function getWarehouseSummaryData($companyId)
    {
        $warehouses = Warehouse::where('company_id', $companyId)->with(['inventories.product'])->get();
        $valuationService = app(InventoryValuationService::class);
        
        $rows = [];

        foreach ($warehouses as $warehouse) {
            $uniqueProducts = 0;
            $totalItems = 0;
            $totalValue = 0;

            foreach ($warehouse->inventories as $inv) {
                if ($inv->available_quantity > 0) {
                    $uniqueProducts++;
                    $totalItems += $inv->available_quantity;
                    $value = $valuationService->calculateValuation($inv->product);
                    // Approximate value for this warehouse based on total value / total qty
                    $totalQty = $inv->product->inventory->sum('available_quantity') ?? 1;
                    if ($totalQty > 0) {
                        $totalValue += ($value / $totalQty) * $inv->available_quantity;
                    }
                }
            }

            $rows[] = [
                'Warehouse' => $warehouse->name,
                'Unique Products' => number_format($uniqueProducts),
                'Total Items' => number_format($totalItems),
                'Est. Value' => '$' . number_format($totalValue, 2),
            ];
        }

        return [
            'title' => 'Warehouse Summary',
            'headers' => ['Warehouse', 'Unique Products', 'Total Items', 'Est. Value'],
            'rows' => $rows
        ];
    }

    private function getTransactionsData($companyId)
    {
        $transactions = InventoryTransaction::where('company_id', $companyId)
            ->with(['inventory.product', 'inventory.warehouse'])
            ->latest()
            ->take(200) // limit for performance in standard report
            ->get();
        
        $rows = [];

        foreach ($transactions as $tx) {
            $rows[] = [
                'Date' => $tx->date->format('Y-m-d H:i'),
                'Warehouse' => $tx->inventory->warehouse->name ?? 'Unknown',
                'Product' => $tx->inventory->product->name ?? 'Unknown',
                'Type' => ucwords(str_replace('_', ' ', $tx->type)),
                'Quantity' => number_format($tx->quantity),
                'Unit Cost' => '$' . number_format($tx->unit_cost ?? 0, 2),
                'Total Value' => '$' . number_format(abs($tx->quantity) * ($tx->unit_cost ?? 0), 2),
            ];
        }

        return [
            'title' => 'Inventory Transactions (Last 200)',
            'headers' => ['Date', 'Warehouse', 'Product', 'Type', 'Quantity', 'Unit Cost', 'Total Value'],
            'rows' => $rows
        ];
    }

    private function getAdjustmentsData($companyId)
    {
        $adjustments = InventoryAdjustment::where('company_id', $companyId)
            ->with(['warehouse', 'items.product'])
            ->latest()
            ->get();
        
        $rows = [];

        foreach ($adjustments as $adj) {
            foreach ($adj->items as $item) {
                $rows[] = [
                    'Date' => $adj->date->format('Y-m-d'),
                    'Warehouse' => $adj->warehouse->name ?? 'Unknown',
                    'Product' => $item->product->name ?? 'Unknown',
                    'Reason' => $adj->reason,
                    'Adjusted Qty' => number_format($item->adjusted_quantity),
                    'Unit Cost' => '$' . number_format($item->unit_cost ?? 0, 2),
                    'Status' => ucfirst($adj->status),
                ];
            }
        }

        return [
            'title' => 'Adjustment Report',
            'headers' => ['Date', 'Warehouse', 'Product', 'Reason', 'Adjusted Qty', 'Unit Cost', 'Status'],
            'rows' => $rows
        ];
    }

    private function getProfitabilityData($companyId)
    {
        $products = Product::where('company_id', $companyId)->where('track_inventory', true)->get();
        $valuationService = app(InventoryValuationService::class);
        
        $rows = [];

        foreach ($products as $product) {
            $unitCost = 0;
            $qty = $product->inventory()->sum('available_quantity') ?? 0;
            if ($qty > 0) {
                $value = $valuationService->calculateValuation($product);
                $unitCost = $value / $qty;
            } else {
                $unitCost = $product->cost_price ?? 0;
            }

            // Calculate units sold
            $unitsSold = abs(InventoryTransaction::whereHas('inventory', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })->whereIn('type', ['sale', 'consumption', 'stock_out'])->sum('quantity') ?? 0);

            if ($unitsSold > 0) {
                $revenue = $unitsSold * ($product->selling_price ?? 0);
                $cogs = $unitsSold * $unitCost;
                $profit = $revenue - $cogs;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

                $rows[] = [
                    'SKU' => $product->sku,
                    'Product' => $product->name,
                    'Units Sold' => number_format($unitsSold),
                    'Avg Unit Cost' => '$' . number_format($unitCost, 2),
                    'Selling Price' => '$' . number_format($product->selling_price ?? 0, 2),
                    'Profit' => '$' . number_format($profit, 2),
                    'Margin %' => number_format($margin, 1) . '%',
                ];
            }
        }

        // Sort by Profit descending
        usort($rows, function($a, $b) {
            return (float)str_replace(['$', ','], '', $b['Profit']) <=> (float)str_replace(['$', ','], '', $a['Profit']);
        });

        return [
            'title' => 'Product Profitability',
            'headers' => ['SKU', 'Product', 'Units Sold', 'Avg Unit Cost', 'Selling Price', 'Profit', 'Margin %'],
            'rows' => $rows
        ];
    }
}
