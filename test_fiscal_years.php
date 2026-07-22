<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$journalService = app(JournalService::class);

echo "\n--- Fiscal Year Validation Tests ---\n\n";

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail();
$revenueAccount = ChartOfAccount::where('code', '4000')->firstOrFail();

// 1. Create a fiscal year
$fiscalYear = FiscalYear::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'FY-2027'],
    ['start_date' => '2027-01-01', 'end_date' => '2027-12-31', 'is_closed' => false]
);

echo "1. Fiscal Year {$fiscalYear->name} created and is currently " . ($fiscalYear->is_closed ? "CLOSED" : "OPEN") . ".\n";

// 2. Post a journal into the open year
try {
    $journal1 = $journalService->createManualJournal(
        ['company_id' => $companyId, 'fiscal_year_id' => $fiscalYear->id, 'date' => now(), 'memo' => 'Test Open FY'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 10, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Rev', 'debit' => 0, 'credit' => 10],
        ]
    );
    $journalService->postJournal($journal1);
    echo "[PASS] Successfully posted journal {$journal1->journal_number} into OPEN Fiscal Year.\n";
} catch (Exception $e) {
    echo "[FAIL] Failed to post into open year: " . $e->getMessage() . "\n";
}

// 3. Close the fiscal year
$fiscalYear->update([
    'is_closed' => true,
    'closed_at' => now(),
    'closed_by' => 1 // Mock user ID
]);
echo "\n3. Fiscal Year {$fiscalYear->name} is now CLOSED.\n";

// 4. Attempt to post into closed year
try {
    $journal2 = $journalService->createManualJournal(
        ['company_id' => $companyId, 'fiscal_year_id' => $fiscalYear->id, 'date' => now(), 'memo' => 'Test Closed FY'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 10, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Rev', 'debit' => 0, 'credit' => 10],
        ]
    );
    $journalService->postJournal($journal2);
    echo "[FAIL] Was able to post journal into a CLOSED Fiscal Year!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'closed Fiscal Year')) {
        echo "[PASS] Prevented posting to closed year: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

// 5. Reopen the fiscal year
$fiscalYear->update([
    'is_closed' => false,
    'closed_at' => null,
    'closed_by' => null
]);
echo "\n5. Fiscal Year {$fiscalYear->name} is REOPENED.\n";

// Refresh the relation so it sees the new is_closed state
$journal2->load('fiscalYear');

// 6. Attempt to post again into the reopened year
try {
    $journalService->postJournal($journal2);
    echo "[PASS] Successfully posted journal {$journal2->journal_number} into REOPENED Fiscal Year.\n";
} catch (Exception $e) {
    echo "[FAIL] Failed to post into reopened year: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
