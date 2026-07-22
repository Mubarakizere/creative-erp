<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cashAccount = App\Models\ChartOfAccount::where('code', '1001')->firstOrFail();
$accountCategory = $cashAccount->accountType->category;
$isDebitNormal = in_array(strtolower($accountCategory), ['asset', 'expense']);

echo "Account: {$cashAccount->name}\n";
echo "Category: {$accountCategory}\n";
echo "Is Debit Normal: " . ($isDebitNormal ? 'Yes' : 'No') . "\n";
