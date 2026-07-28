<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', AssetMaintenance::class);
        
        $maintenances = AssetMaintenance::with('asset')
            ->whereHas('asset', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })
            ->orderBy('maintenance_date', 'desc')
            ->paginate(15);
            
        return view('admin.assets.maintenance.index', compact('maintenances'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AssetMaintenance::class);
        
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'maintenance_date' => 'required|date',
            'description' => 'required|string',
            'vendor' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'warranty_start' => 'nullable|date',
            'warranty_end' => 'nullable|date|after_or_equal:warranty_start',
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
            'status' => 'required|in:Scheduled,In Progress,Completed',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);
        $this->authorize('view', $asset);

        $validated['recorded_by'] = auth()->id();
        
        AssetMaintenance::create($validated);
        
        // Update asset status if under maintenance
        if ($validated['status'] === 'In Progress') {
            $asset->update(['status' => 'Under Maintenance']);
        } elseif ($validated['status'] === 'Completed' && $asset->status === 'Under Maintenance') {
            $asset->update(['status' => 'Active']);
        }
        
        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }
}
