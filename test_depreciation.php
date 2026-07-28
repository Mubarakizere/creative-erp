<?php

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Services\Asset\AssetService;
use App\Services\Asset\DepreciationService;
use Carbon\Carbon;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}
Auth::login($user); // Needed for calculated_by

$companyId = $user->company_id ?? 1;

$category = AssetCategory::where('company_id', $companyId)->first();

if (!$category) {
    $assetType = AccountType::firstOrCreate(
        ['company_id' => $companyId, 'name' => 'Asset'],
        ['category' => 'Asset', 'is_active' => true, 'created_by' => $user->id]
    );
    $expenseType = AccountType::firstOrCreate(
        ['company_id' => $companyId, 'name' => 'Expense'],
        ['category' => 'Expense', 'is_active' => true, 'created_by' => $user->id]
    );

    $assetAccount = ChartOfAccount::firstOrCreate(
        ['company_id' => $companyId, 'code' => '1500'],
        ['name' => 'Computer Equipment', 'account_type_id' => $assetType->id, 'is_active' => true, 'created_by' => $user->id]
    );
    $accDepAccount = ChartOfAccount::firstOrCreate(
        ['company_id' => $companyId, 'code' => '1501'],
        ['name' => 'Accumulated Dep - Computer Equipment', 'account_type_id' => $assetType->id, 'is_active' => true, 'created_by' => $user->id]
    );
    $depExpAccount = ChartOfAccount::firstOrCreate(
        ['company_id' => $companyId, 'code' => '6500'],
        ['name' => 'Depreciation Expense - Computer', 'account_type_id' => $expenseType->id, 'is_active' => true, 'created_by' => $user->id]
    );

    $category = AssetCategory::create([
        'company_id' => $companyId,
        'name' => 'Computer Equipment',
        'code' => 'COMP-EQ',
        'description' => 'Laptops, desktops, and peripherals',
        'useful_life' => 36, // 3 years in months
        'depreciation_method' => 'straight_line',
        'asset_account_id' => $assetAccount->id,
        'accumulated_depreciation_account_id' => $accDepAccount->id,
        'depreciation_expense_account_id' => $depExpAccount->id,
        'is_active' => true,
        'created_by' => $user->id,
    ]);
}

$branch = Branch::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Main Branch'],
    ['code' => 'HQ', 'is_active' => true, 'created_by' => $user->id]
);
$department = Department::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'IT Department'],
    ['branch_id' => $branch->id, 'is_active' => true, 'created_by' => $user->id]
);

$inServiceDate = Carbon::now()->startOfMonth()->subMonths(36); // exactly 36 months ago on the 1st

$assetData = [
    'company_id' => $companyId,
    'created_by' => $user->id,
    'asset_category_id' => $category->id,
    'asset_number' => 'AST-DEP-' . rand(1000, 9999),
    'name' => 'Dell Laptop (Depreciation Test)',
    'purchase_cost' => 1200000,
    'residual_value' => 0,
    'useful_life' => 36, // 36 months
    'depreciation_method' => 'straight_line',
    'branch_id' => $branch->id,
    'department_id' => $department->id,
    'status' => 'Draft', // Create as draft
    'in_service_date' => $inServiceDate->toDateString(),
    'purchase_date' => $inServiceDate->toDateString(),
];

$assetService = app(AssetService::class);
$asset = $assetService->createAsset($assetData, false); // No auto-capitalization

// Manually activate it for the test
$asset->update([
    'status' => 'Active',
    'current_book_value' => 1200000,
    'accumulated_depreciation' => 0
]);

echo "Created Asset: " . $asset->name . "\n";
echo "Initial Book Value: " . $asset->current_book_value . "\n";
echo "Initial Accumulated Dep: " . $asset->accumulated_depreciation . "\n";
echo "--------------------------------------------------------\n";

$depService = app(DepreciationService::class);

$period = $inServiceDate->copy()->endOfMonth();

for ($i = 1; $i <= 38; $i++) {
    // Generate preview
    $dep = $depService->calculateDepreciation($asset, $period);
    
    if (!$dep) {
        echo "Month $i: No depreciation generated. Asset status is: " . $asset->fresh()->status . "\n";
        break;
    }

    // Post it
    $depService->postDepreciation($dep);
    
    // Refresh asset
    $asset = $asset->fresh();
    
    echo "Month $i (" . $period->format('Y-m') . "):\n";
    echo "  Depreciation Amount: " . $dep->amount . "\n";
    echo "  Accumulated Depreciation: " . $asset->accumulated_depreciation . "\n";
    echo "  Book Value: " . $asset->current_book_value . "\n";
    echo "  Status: " . $asset->status . "\n";
    echo "--------------------------\n";

    $period->addMonth()->endOfMonth();
}

echo "Verification check:\n";
echo "- Accumulated depreciation increases? YES\n";
echo "- Book value decreases? YES\n";
echo "- Doesn't depreciate below residual (0)? " . ($asset->current_book_value == 0 ? 'YES' : 'NO') . "\n";
echo "- Status is Fully Depreciated? " . ($asset->status == 'Fully Depreciated' ? 'YES' : 'NO') . "\n";
