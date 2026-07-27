<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehouseTask;
use App\Services\Warehouse\PickingService;
use Illuminate\Http\Request;

class PickingController extends Controller
{
    protected PickingService $pickingService;

    public function __construct(PickingService $pickingService)
    {
        $this->pickingService = $pickingService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseTask::class);
        $companyId = session('company_id') ?? 1;
        
        $items = WarehouseTask::where('company_id', $companyId)
            ->where('type', 'picking')
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
            
        return view('admin.warehouse.picks.index', compact('items'));
    }

    public function edit(WarehouseTask $picking)
    {
        $this->authorize('update', $picking);
        
        $data = json_decode($picking->notes, true);
        $product = \App\Models\Product::find($data['product_id']);
        $bin = \App\Models\WarehouseBin::find($data['bin_id']);
        $allocatedQuantity = $data['quantity'] ?? 0;

        return view('admin.warehouse.picks.edit', ['pick' => $picking, 'product' => $product, 'bin' => $bin, 'allocatedQuantity' => $allocatedQuantity]);
    }

    public function update(Request $request, WarehouseTask $picking)
    {
        $this->authorize('update', $picking);
        
        $data = json_decode($picking->notes, true);
        $allocatedQuantity = $data['quantity'] ?? 0;

        $request->validate([
            'picked_quantity' => 'required|numeric|min:0.01|max:' . $allocatedQuantity,
        ]);
        
        $pickedQty = (float) $request->picked_quantity;
        
        if ($pickedQty >= $allocatedQuantity) {
            $this->pickingService->completePickTask($picking, auth()->id());
            $message = 'Pick completed successfully.';
        } else {
            $this->pickingService->partialPickTask($picking, auth()->id(), $pickedQty);
            $message = 'Partial pick recorded successfully. Remaining quantity requires picking.';
        }
        
        return redirect()->route('admin.warehouse.picking.index')->with('success', $message);
    }
}
