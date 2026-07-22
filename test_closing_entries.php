<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\OpeningBalance;
use App\Models\GeneralLedger;
use App\Models\ClosingEntry;
use App\Services\Finance\AccountingService;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$accountingService = app(AccountingService::class);
$journalService = app(JournalService::class);

auth()->loginUsingId(1);

echo "\n--- Year-End Closing Tests ---\n\n";

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail(); // Asset
$apAccount = ChartOfAccount::where('code', '2000')->firstOrFail();   // Liability
$equityAccount = ChartOfAccount::where('code', '3000')->firstOrFail(); // Equity (Retained Earnings)
$salesAccount = ChartOfAccount::where('code', '4000')->firstOrFail(); // Revenue
$rentAccount = ChartOfAccount::where('code', '5001')->firstOrFail(); // Expense

$fy2030 = FiscalYear::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'FY-2034'],
    ['start_date' => '2034-01-01', 'end_date' => '2034-12-31', 'is_closed' => false]
);

$fy2031 = FiscalYear::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'FY-2035'],
    ['start_date' => '2035-01-01', 'end_date' => '2035-12-31', 'is_closed' => false]
);

// 1. Post Revenue and Expense to FY 2034
$journal = $journalService->createManualJournal(
    ['company_id' => $companyId, 'fiscal_year_id' => $fy2030->id, 'date' => '2034-06-01', 'memo' => 'FY 2034 Ops'],
    [
        ['chart_of_account_id' => $cashAccount->id, 'description' => 'Sales', 'debit' => 1000, 'credit' => 0],
        ['chart_of_account_id' => $salesAccount->id, 'description' => 'Sales', 'debit' => 0, 'credit' => 1000],
        ['chart_of_account_id' => $rentAccount->id, 'description' => 'Rent', 'debit' => 400, 'credit' => 0],
        ['chart_of_account_id' => $cashAccount->id, 'description' => 'Rent', 'debit' => 0, 'credit' => 400],
    ]
);
$journalService->postJournal($journal);
echo "1. Posted operations to FY 2034.\n";

// 2. Close FY 2034
try {
    $closingEntry = $accountingService->closeFiscalYear($companyId, $fy2030->id, $fy2031->id, $equityAccount->id);
    echo "2. [PASS] Fiscal Year 2034 closed successfully.\n";
} catch (Exception $e) {
    echo "2. [FAIL] Failed to close FY 2034: " . $e->getMessage() . "\n";
    exit;
}

// 3. Verify Retained Earnings carried forward to 2035 correctly
$obRE = OpeningBalance::where('fiscal_year_id', $fy2031->id)->where('chart_of_account_id', $equityAccount->id)->first();
if ($obRE && $obRE->credit >= 600) {
    echo "3. [PASS] Retained Earnings carried forward correctly as {$obRE->credit} credit.\n";
} else {
    echo "3. [FAIL] Retained Earnings not carried forward correctly. Got " . ($obRE ? "Dr: {$obRE->debit}, Cr: {$obRE->credit}" : "NULL") . "\n";
}

// 4. Verify Cash Opening Balance for 2035 is prepared
$obCash = OpeningBalance::where('fiscal_year_id', $fy2031->id)->where('chart_of_account_id', $cashAccount->id)->first();
if ($obCash && $obCash->debit >= 600) {
    echo "4. [PASS] Cash opening balance carried forward correctly as {$obCash->debit} debit.\n";
} else {
    echo "4. [FAIL] Cash opening balance not correct. Got " . ($obCash ? "Dr: {$obCash->debit}, Cr: {$obCash->credit}" : "NULL") . "\n";
}

// 5. Verify Revenue and Expense do NOT have opening balances for 2035
$obSales = OpeningBalance::where('fiscal_year_id', $fy2031->id)->where('chart_of_account_id', $salesAccount->id)->first();
if (!$obSales) {
    echo "5. [PASS] Revenue account correctly cleared (no opening balance).\n";
} else {
    echo "5. [FAIL] Revenue account has an opening balance!\n";
}

echo "\nDone.\n";
