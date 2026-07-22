<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = [
    'Current Asset' => 'Asset',
    'Fixed Asset' => 'Asset',
    'Current Liability' => 'Liability',
    'Long-Term Liability' => 'Liability',
    'Owner Equity' => 'Equity',
    'Operating Revenue' => 'Revenue',
    'Operating Expense' => 'Expense',
];

foreach (App\Models\AccountType::all() as $type) {
    if (isset($types[$type->name])) {
        $type->category = $types[$type->name];
        $type->save();
        echo "Updated {$type->name} to category {$type->category}\n";
    }
}
