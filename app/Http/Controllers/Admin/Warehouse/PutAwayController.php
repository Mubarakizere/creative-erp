<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WarehouseTask;
use App\Models\WarehouseBin;
use App\Services\Warehouse\PutAwayService;

class PutAwayController extends Controller
{
    protected PutAwayService $putAwayService;

    public function __construct(PutAwayService $putAwayService)
    {
        $this->putAwayService = $putAwayService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseTask::class);
        $companyId = session('company_id') ?? 1;
        
        $items = WarehouseTask::with(['taskable', 'taskable.product'])
            ->where('company_id', $companyId)
            ->where('type', 'put_away')
            ->latest()
            ->paginate(15);
            
        return view('admin.warehouse.put-aways.index', compact('items'));
    }

    public function edit(WarehouseTask $put_away)
    {
        $this->authorize('update', $put_away);
        
        $item = $put_away->load(['taskable', 'taskable.product']);
        $warehouseId = $put_away->warehouse_id;
        
        // Find suggested bin
        $suggestedBin = $this->putAwayService->suggestBin(
            $put_away->company_id,
            $warehouseId,
            $item->taskable->product_id,
            $item->taskable->quantity_received
        );

        $bins = WarehouseBin::where('company_id', $put_away->company_id)
            ->whereHas('zone', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->where('status', 'active')
            ->get();
            
        return view('admin.warehouse.put-aways.edit', compact('item', 'suggestedBin', 'bins'));
    }

    public function update(Request $request, WarehouseTask $put_away)
    {
        \Illuminate\Support\Facades\Log::info('PutAway update called', ['task' => $put_away->id, 'request' => $request->all()]);
        
        $this->authorize('update', $put_away);
        
        $request->validate([
            'warehouse_bin_id' => 'required|exists:warehouse_bins,id'
        ]);
        
        $bin = WarehouseBin::findOrFail($request->warehouse_bin_id);
        
        // Validate capacity
        if ($bin->capacity) {
            $incomingQty = $put_away->taskable->quantity_received;
            if (($bin->current_quantity + $incomingQty) > $bin->capacity) {
                return back()->withErrors(['warehouse_bin_id' => 'The selected bin does not have enough capacity for this quantity.']);
            }
        }
        
        $this->putAwayService->executePutAway($put_away, $bin->id, auth()->id());
        
        \Illuminate\Support\Facades\Log::info('PutAway executed successfully', ['task' => $put_away->id]);
        
        return redirect()->route('admin.warehouse.put-away.index')
            ->with('success', 'Put Away executed successfully.');
    }
}
