<?php

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = Company::first();
$companyId = $company->id;
$journalService = app(JournalService::class);

$cashAccount = ChartOfAccount::where('code', '1001')->firstOrFail();
$revenueAccount = ChartOfAccount::where('code', '4000')->firstOrFail();

echo "\n--- Double-Entry Validation Tests ---\n\n";

// 1. Debit = Credit -> Success
echo "Test 1: Debit = Credit (Success)\n";
try {
    $journal = $journalService->createManualJournal(
        ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Balanced'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 100, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Revenue', 'debit' => 0, 'credit' => 100],
        ]
    );
    echo "[PASS] Balanced journal created successfully.\n";
} catch (Exception $e) {
    echo "[FAIL] Unexpected exception: " . $e->getMessage() . "\n";
}

// 2. Debit > Credit -> Fail
echo "\nTest 2: Debit > Credit (Fail)\n";
try {
    $journalService->createManualJournal(
        ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Unbalanced D>C'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 150, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Revenue', 'debit' => 0, 'credit' => 100],
        ]
    );
    echo "[FAIL] Unbalanced journal (Debit > Credit) was created!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'must be balanced')) {
        echo "[PASS] Prevented: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

// 3. Credit > Debit -> Fail
echo "\nTest 3: Credit > Debit (Fail)\n";
try {
    $journalService->createManualJournal(
        ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Unbalanced C>D'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Debit Cash', 'debit' => 100, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Credit Revenue', 'debit' => 0, 'credit' => 150],
        ]
    );
    echo "[FAIL] Unbalanced journal (Credit > Debit) was created!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'must be balanced')) {
        echo "[PASS] Prevented: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

// 4. Negative values -> Fail
echo "\nTest 4: Negative values (Fail)\n";
try {
    $journalService->createManualJournal(
        ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Negative Values'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Negative Debit', 'debit' => -100, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Negative Credit', 'debit' => 0, 'credit' => -100],
        ]
    );
    echo "[FAIL] Negative values were allowed!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'cannot have negative values')) {
        echo "[PASS] Prevented: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

// 5. Zero-value journals -> Fail
echo "\nTest 5: Zero-value journals (Fail)\n";
try {
    $journalService->createManualJournal(
        ['company_id' => $companyId, 'date' => now(), 'memo' => 'Test Zero Values'],
        [
            ['chart_of_account_id' => $cashAccount->id, 'description' => 'Zero Debit', 'debit' => 0, 'credit' => 0],
            ['chart_of_account_id' => $revenueAccount->id, 'description' => 'Zero Credit', 'debit' => 0, 'credit' => 0],
        ]
    );
    echo "[FAIL] Zero-value journal was allowed!\n";
} catch (Exception $e) {
    if (str_contains($e->getMessage(), 'Zero-value journals are not permitted')) {
        echo "[PASS] Prevented: " . $e->getMessage() . "\n";
    } else {
        echo "[FAIL] Wrong exception: " . $e->getMessage() . "\n";
    }
}

echo "\nDone.\n";
