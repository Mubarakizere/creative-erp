<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AssetCategory::class, 'asset_category');
    }

    public function index(Request $request)
    {
        $categories = AssetCategory::with(['assetAccount', 'accumulatedDepreciationAccount', 'depreciationExpenseAccount'])
            ->where('company_id', auth()->user()->company_id)
            ->paginate(15);
            
        return view('admin.assets.categories.index', compact('categories'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();
            
        return view('admin.assets.categories.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'useful_life' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,double_declining_balance',
            'asset_account_id' => 'required|exists:chart_of_accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        AssetCategory::create($validated);

        return redirect()->route('admin.asset-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(AssetCategory $assetCategory)
    {
        $accounts = ChartOfAccount::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();
            
        return view('admin.assets.categories.edit', compact('assetCategory', 'accounts'));
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'useful_life' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,double_declining_balance',
            'asset_account_id' => 'required|exists:chart_of_accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();

        $assetCategory->update($validated);

        return redirect()->route('admin.asset-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        if ($assetCategory->assets()->count() > 0) {
            return redirect()->route('admin.asset-categories.index')->with('error', 'Cannot delete category because it has assets.');
        }
        
        $assetCategory->delete();
        
        return redirect()->route('admin.asset-categories.index')->with('success', 'Category deleted successfully.');
    }
}
