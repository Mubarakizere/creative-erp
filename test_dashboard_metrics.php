<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Services\Metrics\AccountingMetrics;

auth()->loginUsingId(1);
$company = Company::first();

echo "\n--- Dashboard & Metrics Tests ---\n\n";

$metricsService = app(AccountingMetrics::class);
$cards = $metricsService->cards();

foreach ($cards as $key => $data) {
    echo str_pad($key, 25) . ": " . $data['value'] . "\n";
}

echo "\nDone.\n";
