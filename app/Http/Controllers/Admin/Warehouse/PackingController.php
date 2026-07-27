<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehousePacking;
use App\Models\WarehousePicking;
use App\Services\Warehouse\PackingService;
use Illuminate\Http\Request;

class PackingController extends Controller
{
    protected PackingService $packingService;

    public function __construct(PackingService $packingService)
    {
        $this->packingService = $packingService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehousePacking::class);
        $companyId = session('company_id') ?? 1;
        
        // Show completed pickings that do not have a completed packing
        $pickings = WarehousePicking::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereDoesntHave('packings', function ($q) {
                $q->where('status', 'completed');
            })
            ->latest()
            ->paginate(15, ['*'], 'pickings_page');

        $packings = WarehousePacking::where('company_id', $companyId)
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'packings_page');

        return view('admin.warehouse.packs.index', compact('pickings', 'packings'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', WarehousePacking::class);
        
        $pickingId = $request->get('picking_id');
        $picking = WarehousePicking::findOrFail($pickingId);
        
        return view('admin.warehouse.packs.create', compact('picking'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WarehousePacking::class);
        
        $request->validate([
            'picking_id' => 'required|exists:warehouse_pickings,id',
        ]);
        
        $picking = WarehousePicking::findOrFail($request->picking_id);
        
        $packing = $this->packingService->createPackingFromPicking($picking, auth()->id());
        
        return redirect()->route('admin.warehouse.packing.edit', $packing)
            ->with('success', 'Packing list created. Please enter dimensions and weight.');
    }

    public function edit(WarehousePacking $packing)
    {
        $this->authorize('update', $packing);
        
        return view('admin.warehouse.packs.edit', compact('packing'));
    }

    public function update(Request $request, WarehousePacking $packing)
    {
        $this->authorize('update', $packing);
        
        $request->validate([
            'total_weight' => 'required|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $this->packingService->completePacking($packing, $request->all(), auth()->id());
        
        return redirect()->route('admin.warehouse.packing.index')
            ->with('success', 'Package marked as completed successfully.');
    }
}
