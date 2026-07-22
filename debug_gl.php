<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = \App\Models\GeneralLedger::with('chartOfAccount.accountType')->get();
$totalDr = 0;
$totalCr = 0;
foreach($entries as $e) {
    $totalDr += $e->debit;
    $totalCr += $e->credit;
}
echo "Total GL Debits: $totalDr, Total GL Credits: $totalCr\n";

$accounts = \App\Models\ChartOfAccount::with('accountType')->get();
foreach($accounts as $acc) {
    $last = \App\Models\GeneralLedger::where('chart_of_account_id', $acc->id)->orderBy('id','desc')->first();
    if($last) {
        $cat = strtolower($acc->accountType->category ?? 'NULL');
        echo "{$acc->name} ({$cat}): {$last->balance}\n";
    }
}
