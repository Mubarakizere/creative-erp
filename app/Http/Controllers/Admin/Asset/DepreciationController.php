<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Services\Asset\DepreciationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepreciationController extends Controller
{
    protected DepreciationService $depreciationService;

    public function __construct(DepreciationService $depreciationService)
    {
        $this->depreciationService = $depreciationService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Asset::class);
        
        $query = AssetDepreciation::with('asset')
            ->whereHas('asset', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $depreciations = $query->orderBy('period_date', 'desc')->paginate(15);
        return view('admin.assets.depreciation.index', compact('depreciations'));
    }

    public function generate(Request $request)
    {
        $this->authorize('depreciate', Asset::class);
        
        $request->validate([
            'period_end' => 'required|date',
        ]);

        $periodEnd = Carbon::parse($request->period_end)->endOfMonth();
        
        $count = $this->depreciationService->generateMonthlyPreview(auth()->user()->company_id, $periodEnd);

        return redirect()->route('admin.asset-depreciations.index', ['status' => 'Preview'])
            ->with('success', "Generated preview for $count assets.");
    }

    public function post(AssetDepreciation $depreciation)
    {
        $this->authorize('depreciate', $depreciation->asset);
        
        try {
            $this->depreciationService->postDepreciation($depreciation);
            return redirect()->back()->with('success', 'Depreciation posted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
