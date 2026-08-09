<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$whs = \App\Models\Warehouse::where('company_id', 1)->get();
echo "Count: " . $whs->count() . "\n";
