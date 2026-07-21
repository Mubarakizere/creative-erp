<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Clients Company IDs: ' . json_encode(\App\Models\Client::pluck('company_id')->unique()) . "\n";
echo 'User 1 Company ID: ' . \App\Models\User::find(1)->company_id . "\n";
