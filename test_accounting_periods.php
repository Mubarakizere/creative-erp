<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$journalService = app(JournalService::class);

echo "\n--- Accounting Period Validation Tests ---\n\n";

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail();
$revenueAccount = ChartOfAccount::where('code', '4000')->firstOrFail();

// 1. Create a fiscal year & period
$fiscalYear = FiscalYear::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'FY-2028'],
    ['start_date' => '2028-01-01', 'end_date' => '2028-12-31', 'is_closed' => false]
);

$period = AccountingPeriod::firstOrCreate(
    ['company_id' => $companyId, 'fiscal_year_id' => $fiscalYear->id, 'name' => 'January 2028'],
    ['start_date' => '2028-01-01', 'end_date' => '2028-01-31', 'status' => 'Open']
);

echo "1. Accounting Period {$period->name} created and is currently {$period->status}.\n";

// 2. Post a journal into the open period
try {
    $journal1 = $journalService->createManualJournal(
        ['company_id' => $companyId, 'fiscal_year_id' => $fiscalYear->id, 'accounting_period_id' => $period->id, 'date' => now(), 'memo' => 'Test Open Period'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 10, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Rev', 'debit' => 0, 'credit' => 10],
        ]
    );
    $journalService->postJournal($journal1);
    echo "[PASS] Successfully posted journal {$journal1->journal_number} into OPEN period.\n";
} catch (Exception $e) {
    echo "[FAIL] Failed to post into open period: " . $e->getMessage() . "\n";
}

// 3. Lock the period
$period->update(['status' => 'Locked']);
echo "\n3. Accounting Period {$period->name} is now LOCKED.\n";

// 4. Attempt to post into locked period
try {
    $journal2 = $journalService->createManualJournal(
        ['company_id' => $companyId, 'fiscal_year_id' => $fiscalYear->id, 'accounting_period_id' => $period->id, 'date' => now(), 'memo' => 'Test Locked Period'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 10, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Rev', 'debit' => 0, 'credit' => 10],
        ]
    );
    $journalService->postJournal($journal2);
    echo "[FAIL] Was able to post journal into a LOCKED period!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'locked Accounting Period')) {
        echo "[PASS] Prevented posting to locked period: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

// 5. Unlock the period
$period->update(['status' => 'Open']);
echo "\n5. Accounting Period {$period->name} is UNLOCKED (Open).\n";

// Refresh the relation so it sees the new status
$journal2->load('accountingPeriod');

// 6. Attempt to post again into the unlocked period
try {
    $journalService->postJournal($journal2);
    echo "[PASS] Successfully posted journal {$journal2->journal_number} into UNLOCKED period.\n";
} catch (Exception $e) {
    echo "[FAIL] Failed to post into unlocked period: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
