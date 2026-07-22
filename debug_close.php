<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companyId = 1;
$fiscalYearId = \App\Models\FiscalYear::where('name', 'FY-2032')->first()->id;
$end_date = '2032-12-31';

$accounts = \App\Models\ChartOfAccount::with('accountType')->where('company_id', $companyId)->get();

$totalAsset = 0;
$totalLiability = 0;
$totalEquity = 0;
$totalRevenue = 0;
$totalExpense = 0;

echo "Balances at $end_date:\n";
foreach ($accounts as $account) {
    $category = strtolower($account->accountType->category ?? 'NULL');
    
    $lastLedger = \App\Models\GeneralLedger::where('company_id', $companyId)
        ->where('chart_of_account_id', $account->id)
        ->where('date', '<=', $end_date)
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->first();
    
    $balance = $lastLedger ? $lastLedger->balance : 0;
    if ($balance == 0) continue;

    echo sprintf("%-30s | %-10s | %10.2f\n", $account->name, $category, $balance);

    if ($category === 'asset') $totalAsset += $balance;
    if ($category === 'liability') $totalLiability += $balance;
    if ($category === 'equity') $totalEquity += $balance;
    if ($category === 'revenue') $totalRevenue += $balance;
    if ($category === 'expense') $totalExpense += $balance;
}

echo "\nTotals:\n";
echo "Asset: $totalAsset\n";
echo "Expense: $totalExpense\n";
echo "Liability: $totalLiability\n";
echo "Equity: $totalEquity\n";
echo "Revenue: $totalRevenue\n";

$debits = $totalAsset + $totalExpense;
$credits = $totalLiability + $totalEquity + $totalRevenue;

echo "\nTotal Debits: $debits\n";
echo "Total Credits: $credits\n";
echo "Difference: " . ($debits - $credits) . "\n";
