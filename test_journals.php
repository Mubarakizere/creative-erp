<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Services\Finance\JournalService;
use App\Services\Finance\LedgerPostingService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;

$journalService = app(JournalService::class);

echo "Fetching accounts...\n";
$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail();
$revenueAccount = ChartOfAccount::where('code', '4000')->firstOrFail();

// Test 1 & 2: Create a balanced journal (Draft) and Cancel it
echo "Test: Create Draft Journal and Cancel it...\n";
$draftJournalToCancel = $journalService->createManualJournal(
    [
        'company_id' => $companyId,
        'date' => now(),
        'memo' => 'Test Draft to Cancel',
    ],
    [
        ['chart_of_account_id' => $cashAccount->id, 'description' => 'Test Debit', 'debit' => 100, 'credit' => 0],
        ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Test Credit', 'debit' => 0, 'credit' => 100],
    ]
);

$cancelledJournal = $journalService->cancelJournal($draftJournalToCancel);
if ($cancelledJournal->status === 'Cancelled') {
    echo "[PASS] Journal {$cancelledJournal->journal_number} cancelled successfully.\n";
} else {
    echo "[FAIL] Journal status is {$cancelledJournal->status}.\n";
}

// Test 3: Create another balanced journal and Post it
echo "Test: Create balanced journal and post it...\n";
$journalToPost = $journalService->createManualJournal(
    [
        'company_id' => $companyId,
        'date' => now(),
        'memo' => 'Test Journal to Post',
    ],
    [
        ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 500, 'credit' => 0],
        ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Revenue', 'debit' => 0, 'credit' => 500],
    ]
);
echo "Created Journal {$journalToPost->journal_number} with status: {$journalToPost->status}\n";

$journalService->postJournal($journalToPost);
$journalToPost->refresh();
if ($journalToPost->status === 'Posted') {
    echo "[PASS] Journal {$journalToPost->journal_number} posted successfully.\n";
} else {
    echo "[FAIL] Journal status is {$journalToPost->status}.\n";
}

// Test 4: Reverse the posted journal
echo "Test: Reverse the posted journal...\n";
$reversalJournal = $journalService->reverseJournal($journalToPost);
$journalToPost->refresh();
if ($journalToPost->status === 'Reversed' && $reversalJournal->status === 'Draft') {
    echo "[PASS] Journal {$journalToPost->journal_number} reversed. Reversal journal {$reversalJournal->journal_number} created.\n";
} else {
    echo "[FAIL] Journal reversal failed.\n";
}

// Test 5: Attempt to create an unbalanced journal
echo "Test: Attempt to create an unbalanced journal...\n";
try {
    $journalService->createManualJournal(
        [
            'company_id' => $companyId,
            'date' => now(),
            'memo' => 'Test Unbalanced Journal',
        ],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 600, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Revenue', 'debit' => 0, 'credit' => 500],
        ]
    );
    echo "[FAIL] Unbalanced journal was created!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'Journal entries must be balanced')) {
        echo "[PASS] Unbalanced journal prevented: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Unexpected exception: " . $e->getMessage() . "\n";
    }
}

echo "Done.\n";
