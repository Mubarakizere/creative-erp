<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$whs = \Illuminate\Support\Facades\DB::table('warehouses')->get();
echo json_encode($whs, JSON_PRETTY_PRINT);
