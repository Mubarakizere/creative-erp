<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = App\Models\GeneralLedger::all();
foreach ($entries as $e) {
    echo "ID: {$e->id}, Acc: {$e->chart_of_account_id}, Date: {$e->date}, Dr: {$e->debit}, Cr: {$e->credit}, Bal: {$e->balance}\n";
}
