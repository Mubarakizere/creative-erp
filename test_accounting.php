<?php

use App\Models\Asset;
use App\Models\Journal;
use App\Models\GeneralLedger;

$asset = Asset::where('name', 'Dell Laptop (Depreciation Test)')->first();

if (!$asset) {
    echo "Asset not found. Run depreciation test first.\n";
    exit;
}

echo "Asset: " . $asset->name . "\n";
echo "Asset ID: " . $asset->id . "\n";

// Get the depreciations posted for this asset
$depreciations = $asset->depreciations()->whereNotNull('journal_id')->get();

if ($depreciations->isEmpty()) {
    echo "No posted depreciations found for this asset.\n";
    exit;
}

$firstDep = $depreciations->first();
$journal = Journal::with(['entries.chartOfAccount.accountType'])->find($firstDep->journal_id);

if (!$journal) {
    echo "Journal not found for first depreciation!\n";
    exit;
}

echo "\n--- JOURNAL ENTRY FOR MONTH 1 DEPRECIATION ---\n";
echo "Journal Number: " . $journal->journal_number . "\n";
echo "Reference: " . $journal->reference_number . "\n";
echo "Date: " . $journal->date->format('Y-m-d') . "\n";
echo "Status: " . $journal->status . "\n";
echo "Total Debit: " . $journal->total_debit . "\n";
echo "Total Credit: " . $journal->total_credit . "\n";

echo "\n--- GENERAL LEDGER ENTRIES ---\n";
$totalDebit = 0;
$totalCredit = 0;

foreach ($journal->entries as $entry) {
    $accountName = $entry->chartOfAccount->name;
    $accountCode = $entry->chartOfAccount->code;
    $accountCategory = $entry->chartOfAccount->accountType->category ?? 'Unknown';
    
    echo sprintf(
        "%-40s | %-10s | Debit: %10s | Credit: %10s\n",
        "[$accountCode] $accountName ($accountCategory)",
        $entry->debit > 0 ? 'DR' : 'CR',
        number_format($entry->debit, 2),
        number_format($entry->credit, 2)
    );
    
    $totalDebit += $entry->debit;
    $totalCredit += $entry->credit;
}

echo "\n----------------------------------------------\n";
echo "Verification:\n";
echo "Calculated Total Debit: " . number_format($totalDebit, 2) . "\n";
echo "Calculated Total Credit: " . number_format($totalCredit, 2) . "\n";
echo "Debit = Credit? " . (round($totalDebit, 2) === round($totalCredit, 2) ? 'YES' : 'NO') . "\n";

$hasExpenseDebit = $journal->entries->contains(function($e) {
    return $e->debit > 0 && str_contains(strtolower($e->chartOfAccount->accountType->category), 'expense');
});
$hasAccumDepCredit = $journal->entries->contains(function($e) {
    return $e->credit > 0 && str_contains(strtolower($e->chartOfAccount->accountType->category), 'asset');
});

echo "Has Depreciation Expense Debit? " . ($hasExpenseDebit ? 'YES' : 'NO') . "\n";
echo "Has Accumulated Depreciation Credit? " . ($hasAccumDepCredit ? 'YES' : 'NO') . "\n";
