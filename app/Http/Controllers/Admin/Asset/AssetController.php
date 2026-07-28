<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use App\Services\Asset\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
        $this->authorizeResource(Asset::class, 'asset');
    }

    public function index(Request $request)
    {
        $query = Asset::with(['category', 'assignedUser', 'department', 'branch'])
            ->where('company_id', auth()->user()->company_id);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->get('category_id') !== '') {
            $query->where('asset_category_id', $request->get('category_id'));
        }

        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $assets = $query->paginate(15)->withQueryString();
        $categories = AssetCategory::where('company_id', auth()->user()->company_id)->get();

        return view('admin.assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $categories = AssetCategory::where('company_id', $companyId)->get();
        $branches = Branch::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $users = User::where('company_id', $companyId)->get();

        return view('admin.assets.create', compact('categories', 'branches', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_number' => 'required|string|unique:assets,asset_number',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'barcode' => 'nullable|string|unique:assets,barcode',
            'purchase_date' => 'nullable|date',
            'in_service_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'residual_value' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,double_declining_balance',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        // Auto-capitalize if requested
        $autoCapitalize = $request->has('auto_capitalize');
        
        $this->assetService->createAsset($validated, $autoCapitalize);

        return redirect()->route('admin.assets.index')->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'assignedUser', 'department', 'branch', 'assignments.user', 'transfers.toUser', 'depreciations', 'maintenances', 'disposals']);
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $companyId = auth()->user()->company_id;
        $categories = AssetCategory::where('company_id', $companyId)->get();
        $branches = Branch::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $users = User::where('company_id', $companyId)->get();

        return view('admin.assets.edit', compact('asset', 'categories', 'branches', 'departments', 'users'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'useful_life' => 'required|integer|min:1',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();
        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->status !== 'Draft') {
            return redirect()->route('admin.assets.index')->with('error', 'Only draft assets can be deleted. Use disposal for active assets.');
        }
        
        $asset->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Asset deleted successfully.');
    }
}
