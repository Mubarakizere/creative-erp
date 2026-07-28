<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Services\Asset\AssetAccountingService;
use Illuminate\Http\Request;

class AssetDisposalController extends Controller
{
    protected AssetAccountingService $accountingService;

    public function __construct(AssetAccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        $this->authorize('viewAny', AssetDisposal::class);
        
        $disposals = AssetDisposal::with('asset')
            ->whereHas('asset', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.assets.disposals.index', compact('disposals'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AssetDisposal::class);
        
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'date' => 'required|date',
            'type' => 'required|in:Disposal,Sale,Write-Off',
            'reason' => 'required|string',
            'sale_price' => 'nullable|numeric|min:0',
            'disposal_costs' => 'nullable|numeric|min:0',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);
        $this->authorize('view', $asset);

        if ($asset->status !== 'Active' && $asset->status !== 'Fully Depreciated') {
            return redirect()->back()->with('error', 'Only active or fully depreciated assets can be disposed.');
        }

        $validated['requested_by'] = auth()->id();
        $validated['status'] = 'Pending Approval';
        
        AssetDisposal::create($validated);
        
        return redirect()->route('admin.asset-disposals.index')->with('success', 'Disposal request submitted for approval.');
    }

    public function approve(AssetDisposal $assetDisposal)
    {
        $this->authorize('approve', $assetDisposal);
        
        try {
            $this->accountingService->postDisposal($assetDisposal);
            return redirect()->back()->with('success', 'Disposal approved and posted to General Ledger.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
