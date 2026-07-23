<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', InventoryAdjustment::class);
        $companyId = session('company_id') ?? 1;

        // Fetch current stock levels
        $inventories = Inventory::where('company_id', $companyId)
            ->with(['product', 'warehouse'])
            ->get();

        // Fetch adjustment requests
        $adjustments = InventoryAdjustment::where('company_id', $companyId)
            ->with(['warehouse', 'approvedBy', 'approval'])
            ->latest()
            ->paginate(15);

        return view('admin.inventory.adjustments.index', compact('inventories', 'adjustments'));
    }

    public function create()
    {
        $this->authorize('create', InventoryAdjustment::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)->where('status', 'active')->get();
        $products = Product::where('company_id', $companyId)->where('status', 'active')->get();

        return view('admin.inventory.adjustments.create', compact('warehouses', 'products'));
    }

    public function store(Request $request, ApprovalService $approvalService)
    {
        $this->authorize('create', InventoryAdjustment::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|in:increase,decrease',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $workflow = ApprovalWorkflow::where('company_id', $companyId)
            ->where('module', 'inventory_adjustments')
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return back()->with('error', 'No active approval workflow found for Inventory Adjustments. Please configure one first.');
        }

        DB::transaction(function () use ($validated, $companyId, $approvalService, $workflow) {
            $adjustment = InventoryAdjustment::create([
                'company_id' => $companyId,
                'warehouse_id' => $validated['warehouse_id'],
                'reason' => $validated['reason'],
                'comments' => $validated['comments'] ?? null,
                'items' => $validated['items'],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            $approvalService->submit($adjustment, $workflow, 'Submitted for inventory adjustment.');
        });

        return redirect()->route('admin.inventory.adjustments.index')
            ->with('success', 'Adjustment request submitted successfully.');
    }
}
