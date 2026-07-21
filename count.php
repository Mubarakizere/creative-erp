<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Clients: ' . \App\Models\Client::count() . "\n";
echo 'Accounts: ' . \App\Models\Account::count() . "\n";
