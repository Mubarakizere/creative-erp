<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransfer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransferController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', InventoryTransfer::class);
        $companyId = session('company_id') ?? 1;

        $transfers = InventoryTransfer::where('company_id', $companyId)
            ->with(['fromWarehouse', 'toWarehouse', 'approval.currentStep', 'transactions'])
            ->latest()
            ->paginate(15);

        return view('admin.inventory.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $this->authorize('create', InventoryTransfer::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('zones')
            ->get();
            
        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        return view('admin.inventory.transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request, ApprovalService $approvalService)
    {
        $this->authorize('create', InventoryTransfer::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'from_zone_id' => 'nullable|exists:warehouse_zones,id',
            'to_zone_id' => 'nullable|exists:warehouse_zones,id',
            'tracking_number' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $workflow = ApprovalWorkflow::where('company_id', $companyId)
            ->where('module', 'inventory_transfers')
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            return back()->with('error', 'No active approval workflow found for Inventory Transfers. Please configure one first.');
        }

        DB::transaction(function () use ($validated, $companyId, $approvalService, $workflow) {
            $transfer = InventoryTransfer::create([
                'company_id' => $companyId,
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'from_zone_id' => $validated['from_zone_id'] ?? null,
                'to_zone_id' => $validated['to_zone_id'] ?? null,
                'tracking_number' => $validated['tracking_number'] ?? null,
                'items' => $validated['items'],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            $transfer->logActivity('approved', ['status' => 'pending', 'created_by' => auth()->id()]);

            $approvalService->submit($transfer, $workflow, 'Submitted for inventory transfer.');
        });

        return redirect()->route('admin.inventory.transfers.index')
            ->with('success', 'Transfer request submitted successfully.');
    }
}
