<?php

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Services\Asset\AssetService;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

$companyId = $user->company_id;

$category = AssetCategory::where('company_id', $companyId)->first();

if (!$category) {
    // Create AccountType if not exists
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

// Setup Branch and Department if they don't exist
$branch = Branch::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Main Branch'],
    ['code' => 'HQ', 'is_active' => true, 'created_by' => $user->id]
);
$department = Department::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'IT Department'],
    ['branch_id' => $branch->id, 'is_active' => true, 'created_by' => $user->id]
);

$assetData = [
    'company_id' => $companyId,
    'created_by' => $user->id,
    'asset_category_id' => $category->id,
    'asset_number' => 'AST-' . rand(1000, 9999),
    'name' => 'Dell Laptop',
    'purchase_cost' => 1200000,
    'residual_value' => 0,
    'useful_life' => 36, // 36 months
    'depreciation_method' => 'straight_line',
    'branch_id' => $branch->id,
    'department_id' => $department->id,
    'status' => 'Active'
];

$service = app(AssetService::class);
$asset = $service->createAsset($assetData, true); // True to auto-capitalize

echo "Successfully created Asset via backend!\n";
echo "ID: " . $asset->id . "\n";
echo "Name: " . $asset->name . "\n";
echo "Cost: " . $asset->purchase_cost . "\n";
echo "Status: " . $asset->status . "\n";
echo "\nFrontend Verification: You can now view the Asset Register to see this Asset.\n";
