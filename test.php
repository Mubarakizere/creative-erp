<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$whs = \Illuminate\Support\Facades\DB::table('warehouses')->get();
foreach($whs as $w) {
    echo "ID: " . $w->id . " | Name: " . $w->name . " | Company ID: " . $w->company_id . " | Deleted: " . $w->deleted_at . "\n";
}
