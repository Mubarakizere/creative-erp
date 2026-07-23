<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissions = [
    'supplier.view', 'supplier.create', 'supplier.update', 'supplier.delete',
    'procurement.view', 'procurement.create', 'procurement.approve',
    'purchase_order.view', 'purchase_order.create', 'purchase_order.approve',
    'goods_receipt.create',
    'supplier_payment.view', 'supplier_payment.create',
    'supplier_report.view'
];

foreach ($permissions as $p) {
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
}
echo "Permissions created successfully.\n";
