<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\Journal;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$journalService = app(JournalService::class);

echo "\n--- General Ledger Validation Tests ---\n\n";

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail();
$revenueAccount = ChartOfAccount::where('code', '4000')->firstOrFail();

// Get initial balances
$initialCashBalance = GeneralLedger::where('chart_of_account_id', $cashAccount->id)
    ->orderBy('date', 'desc')->orderBy('id', 'desc')->first()->balance ?? 0;
$initialRevBalance = GeneralLedger::where('chart_of_account_id', $revenueAccount->id)
    ->orderBy('date', 'desc')->orderBy('id', 'desc')->first()->balance ?? 0;

echo "Initial Cash Balance: $initialCashBalance\n";
echo "Initial Revenue Balance: $initialRevBalance\n";

// 1. Create and Post a Journal
$journal = $journalService->createManualJournal(
    ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Ledger Posting'],
    [
        ['chart_of_account_id' => $cashAccount->id, 'description' => 'Ledger Debit Cash', 'debit' => 250, 'credit' => 0],
        ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Ledger Credit Revenue', 'debit' => 0, 'credit' => 250],
    ]
);

$journalService->postJournal($journal);
echo "Journal {$journal->journal_number} posted successfully.\n";

// 2. Verify ledger entries are created
$cashLedgerEntry = GeneralLedger::where('chart_of_account_id', $cashAccount->id)
    ->where('source_type', Journal::class)
    ->where('source_id', $journal->id)
    ->first();

$revLedgerEntry = GeneralLedger::where('chart_of_account_id', $revenueAccount->id)
    ->where('source_type', Journal::class)
    ->where('source_id', $journal->id)
    ->first();

if ($cashLedgerEntry && $revLedgerEntry) {
    echo "[PASS] Ledger entries were created.\n";
} else {
    echo "[FAIL] Ledger entries were NOT created.\n";
}

// 3. Confirm running balances update correctly
$expectedCashBalance = $initialCashBalance + 250; // Asset normal balance is Debit
$expectedRevBalance = $initialRevBalance + 250;   // Revenue normal balance is Credit (Debit decreases, Credit increases)

if ($cashLedgerEntry->balance == $expectedCashBalance) {
    echo "[PASS] Cash running balance updated correctly to $expectedCashBalance.\n";
} else {
    echo "[FAIL] Expected Cash balance $expectedCashBalance, got {$cashLedgerEntry->balance}.\n";
}

if ($revLedgerEntry->balance == $expectedRevBalance) {
    echo "[PASS] Revenue running balance updated correctly to $expectedRevBalance.\n";
} else {
    echo "[FAIL] Expected Revenue balance $expectedRevBalance, got {$revLedgerEntry->balance}.\n";
}

// 4. Ensure each ledger entry references its source journal
if ($cashLedgerEntry->source_type === Journal::class && $cashLedgerEntry->source_id === $journal->id) {
    echo "[PASS] Ledger entry correctly references source journal.\n";
} else {
    echo "[FAIL] Ledger entry reference mismatch.\n";
}

// 5. Verify company isolation
if ($cashLedgerEntry->company_id === $companyId) {
    echo "[PASS] Company isolation maintained correctly on ledger entry.\n";
} else {
    echo "[FAIL] Company ID mismatch on ledger entry.\n";
}

echo "\nDone.\n";
