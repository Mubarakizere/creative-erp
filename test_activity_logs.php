<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;
use App\Services\Finance\JournalService;
use App\Services\Finance\FiscalPeriodService;
use App\Models\Journal;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;

auth()->loginUsingId(1);
$companyId = 1;

// Generate missing logs first
$journalService = app(JournalService::class);
$fiscalPeriodService = app(FiscalPeriodService::class);

$journal = Journal::where('company_id', $companyId)->where('status', 'Posted')->first();
if ($journal) {
    try {
        $journalService->reverseJournal($journal, ['memo' => 'Test reversal', 'date' => now()->format('Y-m-d')]);
    } catch (\Throwable $e) {}
}

$fiscalYear = FiscalYear::where('company_id', $companyId)->first();
if (!$fiscalYear) {
    $fiscalYear = $fiscalPeriodService->createFiscalYear($companyId, 'Test FY', now()->format('Y-m-d'), now()->addYear()->format('Y-m-d'));
} else {
    // Just force a log to be created by creating a dummy one and then rolling back or catching error
    try {
        $fiscalPeriodService->createFiscalYear($companyId, 'Test FY2', now()->addYears(2)->format('Y-m-d'), now()->addYears(3)->format('Y-m-d'));
    } catch (\Throwable $e) {}
}

$period = AccountingPeriod::where('company_id', $companyId)->where('is_locked', false)->first();
if ($period) {
    try {
        $fiscalPeriodService->closeAccountingPeriod($period);
    } catch (\Throwable $e) {}
}

echo "\n--- Activity Logs Tests ---\n\n";

$expectedActions = [
    'Account creation' => 'account_created',
    'Journal creation' => 'journal_created',
    'Journal posting' => 'journal_posted',
    'Journal reversal' => 'journal_reversed',
    'Fiscal year changes' => 'fiscal_year_created', // or fiscal_year_closed
    'Period locking/unlocking' => 'accounting_period_closed',
    'Opening balance imports' => 'opening_balances_imported',
    'Closing entries' => 'fiscal_year_closed',
];

foreach ($expectedActions as $label => $actionStr) {
    $count = ActivityLog::where('action', $actionStr)->count();
    if ($count > 0) {
        echo "   [PASS] Found $count logs for: $label ($actionStr)\n";
    } else {
        echo "   [FAIL] No logs found for: $label ($actionStr)\n";
    }
}

echo "\nDone.\n";
