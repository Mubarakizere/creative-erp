<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\OpeningBalance;
use App\Models\GeneralLedger;
use App\Models\ActivityLog;
use App\Services\Finance\AccountingService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$accountingService = app(AccountingService::class);

// Login a user to trigger ActivityLog
auth()->loginUsingId(1);

echo "\n--- Opening Balances Import Tests ---\n\n";

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail(); // Asset
$apAccount = ChartOfAccount::where('code', '2000')->firstOrFail();   // Liability
$equityAccount = ChartOfAccount::where('code', '3000')->firstOrFail(); // Equity

$fiscalYear = FiscalYear::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'FY-2029'],
    ['start_date' => '2029-01-01', 'end_date' => '2029-12-31', 'is_closed' => false]
);

$importData = [
    [
        'chart_of_account_id' => $cashAccount->id,
        'debit' => 10000,
        'credit' => 0,
        'import_date' => '2029-01-01',
    ],
    [
        'chart_of_account_id' => $apAccount->id,
        'debit' => 0,
        'credit' => 4000,
        'import_date' => '2029-01-01',
    ],
    [
        'chart_of_account_id' => $equityAccount->id,
        'debit' => 0,
        'credit' => 6000,
        'import_date' => '2029-01-01',
    ],
];

// 1. Import Opening Balances
try {
    $journal = $accountingService->importOpeningBalances($importData, $companyId, $fiscalYear->id);
    echo "1. [PASS] Opening balances imported successfully. Journal {$journal->journal_number} created.\n";
} catch (Exception $e) {
    echo "1. [FAIL] Import failed: " . $e->getMessage() . "\n";
    exit;
}

// 2. Verify balances appear in the OpeningBalance table
$obCash = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)->where('chart_of_account_id', $cashAccount->id)->first();
$obAP = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)->where('chart_of_account_id', $apAccount->id)->first();
$obEquity = OpeningBalance::where('fiscal_year_id', $fiscalYear->id)->where('chart_of_account_id', $equityAccount->id)->first();

if ($obCash && $obCash->debit == 10000 && $obAP && $obAP->credit == 4000 && $obEquity && $obEquity->credit == 6000) {
    echo "2. [PASS] OpeningBalance records created correctly.\n";
} else {
    echo "2. [FAIL] OpeningBalance records are missing or incorrect.\n";
}

// 3. Verify balances appear in the General Ledger
$ledgerCash = GeneralLedger::where('source_id', $journal->id)->where('chart_of_account_id', $cashAccount->id)->first();
$ledgerAP = GeneralLedger::where('source_id', $journal->id)->where('chart_of_account_id', $apAccount->id)->first();

if ($ledgerCash && $ledgerCash->debit == 10000 && $ledgerAP && $ledgerAP->credit == 4000) {
    echo "3. [PASS] General Ledger correctly updated with Opening Balances.\n";
} else {
    echo "3. [FAIL] General Ledger missing expected Opening Balances.\n";
}

// 4. Check audit logs
$logExists = ActivityLog::where('action', 'opening_balances_imported')
    ->where('subject_type', get_class($journal))
    ->where('subject_id', $journal->id)
    ->exists();

if ($logExists) {
    echo "4. [PASS] Audit log 'opening_balances_imported' verified.\n";
} else {
    echo "4. [FAIL] Audit log 'opening_balances_imported' not found.\n";
}

echo "\nDone.\n";
