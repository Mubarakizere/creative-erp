<?php

use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\User;
use App\Models\Company;

$user = User::first();
if (!$user) {
    echo "No user found to run test.\n";
    exit;
}

$companyId = $user->company_id;

// Create AccountType if not exists
$assetType = AccountType::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Asset'],
    ['category' => 'Asset', 'is_active' => true, 'created_by' => $user->id]
);
$expenseType = AccountType::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Expense'],
    ['category' => 'Expense', 'is_active' => true, 'created_by' => $user->id]
);
$liabilityType = AccountType::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Liability'],
    ['category' => 'Liability', 'is_active' => true, 'created_by' => $user->id]
);

// We need some chart of accounts to link to
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

echo "Successfully created Asset Category via backend!\n";
echo "ID: " . $category->id . "\n";
echo "Name: " . $category->name . "\n";
echo "Useful Life: " . $category->useful_life . " months\n";
echo "Method: " . $category->depreciation_method . "\n";
echo "\nFrontend Verification: You can now go to your browser and navigate to the Asset Categories menu to view it.\n";
