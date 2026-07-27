<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\WarehouseBin;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Support\Str;

$company = Company::first();
if (!$company) { echo "No company found"; exit; }

$warehouse = Warehouse::firstOrCreate([
    'company_id' => $company->id, 'code' => 'SW-01'
], ['name' => 'Seeded Warehouse', 'status' => 'active']);

$zone = WarehouseZone::firstOrCreate([
    'company_id' => $company->id, 'warehouse_id' => $warehouse->id, 'code' => 'REC'
], ['name' => 'Receiving', 'type' => 'receiving', 'status' => 'active']);

$bin = WarehouseBin::firstOrCreate([
    'company_id' => $company->id, 'warehouse_zone_id' => $zone->id, 'code' => 'BIN-A1'
], ['capacity' => 1000, 'status' => 'active']);

$supplier = Supplier::firstOrCreate([
    'company_id' => $company->id, 'code' => 'SUP-SEED'
], ['name' => 'Seeded Supplier']);

$product = Product::firstOrCreate([
    'company_id' => $company->id, 'sku' => 'PROD-SEED'
], ['name' => 'Seeded Product', 'product_type' => 'physical', 'unit_of_measure' => 'pcs']);

$service = app(GoodsReceiptService::class);

for ($i=1; $i<=3; $i++) {
    auth()->loginUsingId(1); // Ensure auth works for created_by
    $service->create([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'supplier_id' => $supplier->id,
        'code' => 'GR-SEED-' . Str::random(4),
        'receipt_date' => now(),
        'status' => 'completed',
    ], [
        [
            'product_id' => $product->id,
            'quantity_received' => rand(10, 50),
            'unit_price' => rand(10, 100),
        ]
    ]);
}

echo "3 Put Away tasks seeded successfully!";
