<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accounts = \App\Models\ChartOfAccount::with('accountType')->get();

foreach($accounts as $acc) {
    $entries = \App\Models\GeneralLedger::where('chart_of_account_id', $acc->id)->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
    
    $cat = strtolower($acc->accountType->category ?? 'NULL');
    $isDebitNormal = in_array($cat, ['asset', 'expense']);
    
    $runningBalance = 0;
    foreach($entries as $e) {
        if ($isDebitNormal) {
            $runningBalance += $e->debit - $e->credit;
        } else {
            $runningBalance += $e->credit - $e->debit;
        }
        $e->balance = $runningBalance;
        $e->save();
    }
}
echo "Fixed all GeneralLedger balances.\n";
