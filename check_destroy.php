<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'QuotationController destroy exists: ' . (method_exists(\App\Http\Controllers\QuotationController::class, 'destroy') ? 'Yes' : 'No') . "\n";
