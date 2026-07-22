<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Services\Finance\AccountingReportService;

auth()->loginUsingId(1);
$company = Company::first();
$companyId = $company->id;

echo "\n--- Accounting Reports Tests ---\n\n";

$reportService = app(AccountingReportService::class);
$fiscalYear = FiscalYear::where('company_id', $companyId)->latest()->first();
$cashAccount = ChartOfAccount::where('company_id', $companyId)->where('name', 'Main Bank Account')->first();

echo "1. Trial Balance:\n";
$tb = $reportService->generateTrialBalance($companyId, $fiscalYear->id ?? null);
$totalDr = 0;
$totalCr = 0;
foreach ($tb as $item) {
    if ($item['balance'] != 0) {
        $balType = $item['balance_type'];
        echo "   - {$item['account_name']}: {$item['balance']} ($balType)\n";
        if ($balType === 'Debit') $totalDr += $item['balance'];
        else $totalCr += $item['balance'];
    }
}
echo "   Total Debits: $totalDr, Total Credits: $totalCr\n\n";

echo "2. General Ledger / Account Activity (Main Bank Account):\n";
if ($cashAccount) {
    $gl = $reportService->generateGeneralLedgerReport($companyId, $cashAccount->id);
    echo "   Fetched " . $gl->count() . " entries for {$cashAccount->name}.\n";
    $lastEntry = $gl->last();
    echo "   Ending Balance: " . ($lastEntry['balance'] ?? 0) . "\n\n";
} else {
    echo "   [FAIL] Cash Account not found.\n\n";
}

echo "3. Journal Report:\n";
$journals = $reportService->generateJournalReport($companyId);
echo "   Fetched " . $journals->count() . " distinct journals.\n\n";

echo "4. Fiscal Year Summary:\n";
if ($fiscalYear) {
    $fys = $reportService->generateFiscalYearSummary($companyId, $fiscalYear->id);
    echo "   Total Assets: {$fys['total_assets']}\n";
    echo "   Total Liabilities: {$fys['total_liabilities']}\n";
    echo "   Total Equity: {$fys['total_equity']}\n";
    echo "   Net Income: {$fys['net_income']}\n";
} else {
    echo "   [FAIL] Fiscal Year not found.\n";
}

echo "\nDone.\n";
