<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Services\Procurement\PurchaseRequisitionService;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    protected PurchaseRequisitionService $service;

    public function __construct(PurchaseRequisitionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseRequisition::class);
        return response()->json($this->service->list($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $data = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);
        $items = $data['items'];
        unset($data['items']);
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('view', $purchaseRequisition);
        $purchaseRequisition->load(['items.product', 'requestedBy', 'department', 'project']);
        return response()->json($purchaseRequisition);
    }

    public function approve(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('approve', $purchaseRequisition);
        return response()->json($this->service->approve($purchaseRequisition));
    }
}