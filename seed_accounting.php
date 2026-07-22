<?php

use App\Models\Company;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Services\Finance\JournalService;
use App\Services\Finance\LedgerPostingService;
use Illuminate\Database\QueryException;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first() ?? Company::create(['name' => 'Acme Corp']);
$companyId = $company->id;

echo "Seeding Account Types...\n";
$types = [
    ['name' => 'Current Asset', 'category' => 'Asset'],
    ['name' => 'Fixed Asset', 'category' => 'Asset'],
    ['name' => 'Current Liability', 'category' => 'Liability'],
    ['name' => 'Long-Term Liability', 'category' => 'Liability'],
    ['name' => 'Owner Equity', 'category' => 'Equity'],
    ['name' => 'Operating Revenue', 'category' => 'Revenue'],
    ['name' => 'Operating Expense', 'category' => 'Expense'],
];

$typeMap = [];
foreach ($types as $t) {
    $typeMap[$t['name']] = AccountType::firstOrCreate(
        ['name' => $t['name'], 'company_id' => $companyId],
        ['category' => $t['category'], 'description' => $t['name']]
    );
}

echo "Seeding Chart of Accounts...\n";

// Asset
$cashParent = ChartOfAccount::firstOrCreate(
    ['code' => '1000', 'company_id' => $companyId],
    ['name' => 'Cash and Equivalents', 'account_type_id' => $typeMap['Current Asset']->id]
);
$cashBank = ChartOfAccount::firstOrCreate(
    ['code' => '1001', 'company_id' => $companyId],
    ['name' => 'Main Bank Account', 'account_type_id' => $typeMap['Current Asset']->id, 'parent_id' => $cashParent->id]
);

// Liability
$apParent = ChartOfAccount::firstOrCreate(
    ['code' => '2000', 'company_id' => $companyId],
    ['name' => 'Accounts Payable', 'account_type_id' => $typeMap['Current Liability']->id]
);

// Equity
$equity = ChartOfAccount::firstOrCreate(
    ['code' => '3000', 'company_id' => $companyId],
    ['name' => 'Retained Earnings', 'account_type_id' => $typeMap['Owner Equity']->id]
);

// Revenue
$sales = ChartOfAccount::firstOrCreate(
    ['code' => '4000', 'company_id' => $companyId],
    ['name' => 'Sales Revenue', 'account_type_id' => $typeMap['Operating Revenue']->id]
);

// Expense
$rentParent = ChartOfAccount::firstOrCreate(
    ['code' => '5000', 'company_id' => $companyId],
    ['name' => 'Facilities Expense', 'account_type_id' => $typeMap['Operating Expense']->id]
);
$rentOffice = ChartOfAccount::firstOrCreate(
    ['code' => '5001', 'company_id' => $companyId],
    ['name' => 'Office Rent', 'account_type_id' => $typeMap['Operating Expense']->id, 'parent_id' => $rentParent->id]
);

echo "Testing unique account code constraint...\n";
try {
    ChartOfAccount::create([
        'company_id' => $companyId,
        'code' => '1000', // Already used
        'name' => 'Duplicate Cash',
        'account_type_id' => $typeMap['Current Asset']->id
    ]);
    echo "[FAIL] Duplicate account code was allowed!\n";
} catch (QueryException $e) {
    echo "[PASS] Duplicate account code prevented by DB.\n";
}

echo "Testing deactivated account in journal entries...\n";
// Create an inactive account
$inactiveAccount = ChartOfAccount::firstOrCreate(
    ['code' => '9999', 'company_id' => $companyId],
    ['name' => 'Old Bank Account', 'account_type_id' => $typeMap['Current Asset']->id, 'is_active' => false]
);

$journalService = app(JournalService::class);

try {
    $journalService->createManualJournal(
        [
            'company_id' => $companyId,
            'date' => now(),
            'memo' => 'Test Inactive Account',
        ],
        [
            ['chart_of_account_id' => $inactiveAccount->id, 'description' => 'Test Debit', 'debit' => 100, 'credit' => 0],
            ['chart_of_account_id' => $cashBank->id, 'description' => 'Test Credit', 'debit' => 0, 'credit' => 100],
        ]
    );
    echo "[FAIL] Inactive account was allowed in journal entry!\n";
} catch (Exception $e) {
    echo "[PASS] Inactive account prevented: " . $e->getMessage() . "\n";
}

echo "Done.\n";
