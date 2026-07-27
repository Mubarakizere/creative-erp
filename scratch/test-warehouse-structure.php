<?php
// scratch/test-warehouse-structure.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\WarehouseBin;

$company = Company::first();

if (!$company) {
    $company = Company::create([
        'name' => 'Creative ERP Test Company',
        'email' => 'test@creativeerp.test',
        'status' => 'active'
    ]);
}

echo "Testing Warehouse Structure for Company: " . $company->name . "\n";

// 1. Create a Warehouse
$warehouse = Warehouse::create([
    'company_id' => $company->id,
    'name' => 'Main Distribution Center ' . rand(1, 1000),
    'location' => '123 Logistics Way, NY',
    'status' => 'active',
    'is_default' => true
]);
echo "Created Warehouse: " . $warehouse->name . "\n";

// 2. Create Warehouse Zones
$zoneA = WarehouseZone::create([
    'company_id' => $company->id,
    'warehouse_id' => $warehouse->id,
    'name' => 'Cold Storage Zone',
    'type' => 'cold',
    'status' => 'active'
]);
echo "Created Zone: " . $zoneA->name . "\n";

$zoneB = WarehouseZone::create([
    'company_id' => $company->id,
    'warehouse_id' => $warehouse->id,
    'name' => 'Bulk Storage Zone',
    'type' => 'bulk',
    'status' => 'active'
]);
echo "Created Zone: " . $zoneB->name . "\n";

// 3. Create Bins (Aisles, Racks, Shelves) & Assign to Zones & Set Capacities
// Cold Storage - Aisle 1, Rack A, Shelves 1-3
for ($i = 1; $i <= 3; $i++) {
    $bin = WarehouseBin::create([
        'company_id' => $company->id,
        'warehouse_zone_id' => $zoneA->id,
        'code' => "CS-A1-RA-S{$i}-" . rand(1, 1000),
        'aisle' => 'A1',
        'rack' => 'A',
        'shelf' => "{$i}",
        'capacity' => 100.00,
        'status' => 'active',
        'allowed_product_types' => json_encode(['perishable', 'frozen'])
    ]);
    echo "Created Bin: " . $bin->code . " in Zone " . $zoneA->name . "\n";
}

// Bulk Storage - Aisle 2, Rack B, Shelves 1-2
for ($i = 1; $i <= 2; $i++) {
    $bin = WarehouseBin::create([
        'company_id' => $company->id,
        'warehouse_zone_id' => $zoneB->id,
        'code' => "BS-A2-RB-S{$i}-" . rand(1, 1000),
        'aisle' => 'A2',
        'rack' => 'B',
        'shelf' => "{$i}",
        'capacity' => 500.00,
        'status' => ($i == 1) ? 'full' : 'maintenance', // Test different statuses
        'allowed_product_types' => json_encode(['electronics', 'furniture'])
    ]);
    echo "Created Bin: " . $bin->code . " in Zone " . $zoneB->name . " (Status: " . $bin->status . ")\n";
}

echo "\nVerification:\n";
$warehouse->load('zones.bins');
foreach ($warehouse->zones as $zone) {
    echo "Zone: {$zone->name} has " . $zone->bins()->count() . " bins.\n";
    foreach ($zone->bins as $bin) {
        echo "  - Bin {$bin->code}: Aisle {$bin->aisle}, Rack {$bin->rack}, Shelf {$bin->shelf} | Capacity: {$bin->capacity} | Status: {$bin->status}\n";
    }
}

echo "\nWarehouse structure test completed successfully.\n";
