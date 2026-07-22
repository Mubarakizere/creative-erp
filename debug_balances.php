<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accounts = \App\Models\ChartOfAccount::with('accountType')->get();

foreach($accounts as $acc) {
    $entries = \App\Models\GeneralLedger::where('chart_of_account_id', $acc->id)->get();
    $sumDr = $entries->sum('debit');
    $sumCr = $entries->sum('credit');
    $last = $entries->sortByDesc('id')->first();
    $balanceField = $last ? $last->balance : 0;
    
    $cat = strtolower($acc->accountType->category ?? 'NULL');
    $isDebitNormal = in_array($cat, ['asset', 'expense']);
    
    $calculatedBalance = $isDebitNormal ? ($sumDr - $sumCr) : ($sumCr - $sumDr);
    
    echo sprintf("%-30s | %-10s | %10.2f Dr | %10.2f Cr | Calc Bal: %10.2f | Field: %10.2f", 
        $acc->name, $cat, $sumDr, $sumCr, $calculatedBalance, $balanceField);
    
    if (round($calculatedBalance, 2) !== round((float)$balanceField, 2)) {
        echo " <--- MISMATCH!";
    }
    echo "\n";
}
