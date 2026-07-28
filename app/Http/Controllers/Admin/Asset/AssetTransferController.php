<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Services\Asset\AssetService;
use Illuminate\Http\Request;

class AssetTransferController extends Controller
{
    protected AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index()
    {
        $this->authorize('viewAny', AssetTransfer::class);
        
        $transfers = AssetTransfer::with(['asset', 'fromUser', 'toUser', 'fromDepartment', 'toDepartment'])
            ->whereHas('asset', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.assets.transfers.index', compact('transfers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AssetTransfer::class);
        
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'to_user_id' => 'nullable|exists:users,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'to_branch_id' => 'nullable|exists:branches,id',
            'to_warehouse_id' => 'nullable|exists:warehouses,id',
            'transfer_date' => 'required|date',
            'reason' => 'required|string',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);
        $this->authorize('view', $asset);

        $this->assetService->initiateTransfer($asset, $validated);

        return redirect()->route('admin.asset-transfers.index')->with('success', 'Transfer initiated successfully.');
    }

    public function approve(AssetTransfer $assetTransfer)
    {
        $this->authorize('approve', $assetTransfer);
        
        try {
            $this->assetService->executeTransfer($assetTransfer);
            return redirect()->back()->with('success', 'Transfer approved and executed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(AssetTransfer $assetTransfer)
    {
        $this->authorize('approve', $assetTransfer); // Same permission as approve
        
        $this->assetService->rejectTransfer($assetTransfer);
        return redirect()->back()->with('success', 'Transfer rejected.');
    }
}
