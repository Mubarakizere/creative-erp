<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransfer;
use App\Models\InventoryAdjustment;
use App\Services\Inventory\InventoryEngine;
use App\Services\ApprovalService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    protected $inventoryEngine;
    protected $approvalService;
    protected $workflowService;

    public function __construct(
        InventoryEngine $inventoryEngine,
        ApprovalService $approvalService,
        WorkflowService $workflowService
    ) {
        $this->inventoryEngine = $inventoryEngine;
        $this->approvalService = $approvalService;
        $this->workflowService = $workflowService;
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $inventory = Inventory::where('company_id', $companyId)
            ->with(['product', 'warehouse'])
            ->paginate($request->get('per_page', 15));

        return response()->json($inventory);
    }

    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $companyId = auth()->user()->company_id;

            $transfer = InventoryTransfer::create([
                'company_id' => $companyId,
                'tracking_number' => 'TRF-' . strtoupper(uniqid()),
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'status' => 'pending',
                'items' => $validated['items'],
                'created_by' => auth()->id(),
            ]);

            // Check if workflow is required
            $workflow = $this->workflowService->getActiveWorkflowForModule('Inventory Transfer', $companyId);
            
            if ($workflow) {
                $this->approvalService->submit($transfer, $workflow, 'Submitted for approval.');
            } else {
                // If no workflow, auto-approve and execute
                $transfer->update(['status' => 'approved']);
                $this->inventoryEngine->transfer($transfer, auth()->id());
            }

            return response()->json($transfer->load('approval'), 201);
        });
    }

    public function adjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:addition,deduction,count',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.type' => 'required|in:increase,decrease',
            'reason' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $companyId = auth()->user()->company_id;

            $adjustment = InventoryAdjustment::create([
                'company_id' => $companyId,
                'warehouse_id' => $validated['warehouse_id'],
                'status' => 'pending',
                'reason' => $validated['reason'] ?? null,
                'items' => $validated['items'],
                'created_by' => auth()->id(),
            ]);

            $workflow = $this->workflowService->getActiveWorkflowForModule('Inventory Adjustment', $companyId);
            
            if ($workflow) {
                $this->approvalService->submit($adjustment, $workflow, 'Submitted for approval.');
            } else {
                // If no workflow, auto-approve and execute
                $adjustment->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                $this->inventoryEngine->applyAdjustment($adjustment, auth()->id());
            }

            return response()->json($adjustment->load('approval'), 201);
        });
    }
}
