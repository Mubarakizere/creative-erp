<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseZone;
use Illuminate\Support\Facades\DB;
use App\Services\Warehouse\WarehouseMovementService;

echo "Seeding Movement Scenario...\n";

$company = Company::first();
if (!$company) {
    die("No company found.\n");
}

$warehouse = Warehouse::where('company_id', $company->id)->first();
if (!$warehouse) {
    die("No warehouse found.\n");
}

// Ensure we have some zones and bins
$sourceZone = WarehouseZone::firstOrCreate(
    ['warehouse_id' => $warehouse->id, 'company_id' => $company->id, 'name' => 'Storage Zone A'],
    ['type' => 'storage']
);
$sourceBin = WarehouseBin::firstOrCreate(
    ['warehouse_zone_id' => $sourceZone->id, 'warehouse_id' => $warehouse->id, 'company_id' => $company->id, 'code' => 'A-01-01'],
    ['capacity' => 1000]
);

$destZone = WarehouseZone::firstOrCreate(
    ['warehouse_id' => $warehouse->id, 'company_id' => $company->id, 'name' => 'Storage Zone B'],
    ['type' => 'storage']
);
$destBin = WarehouseBin::firstOrCreate(
    ['warehouse_zone_id' => $destZone->id, 'warehouse_id' => $warehouse->id, 'company_id' => $company->id, 'code' => 'B-01-01'],
    ['capacity' => 1000]
);

// Ensure we have a product with some inventory
$inventory = Inventory::where('company_id', $company->id)
    ->where('warehouse_id', $warehouse->id)
    ->whereNotNull('warehouse_bin_id')
    ->where('available_quantity', '>', 5)
    ->first();

if (!$inventory) {
    die("No inventory found with sufficient quantity. Please seed some inventory first.\n");
}

DB::transaction(function () use ($company, $warehouse, $sourceZone, $sourceBin, $destZone, $destBin, $inventory) {
    $userId = \App\Models\User::first()->id;
    $service = app(WarehouseMovementService::class);

    // 1. Bin to Bin Movement Request (Pending)
    $movement1 = $service->requestMovement([
        'company_id' => $company->id,
        'type' => 'bin_to_bin',
        'source_warehouse_id' => $warehouse->id,
        'source_zone_id' => $inventory->warehouse_zone_id,
        'source_bin_id' => $inventory->warehouse_bin_id,
        'destination_warehouse_id' => $warehouse->id,
        'destination_zone_id' => $destZone->id,
        'destination_bin_id' => $destBin->id,
        'product_id' => $inventory->product_id,
        'quantity' => 2,
        'reason' => 'Consolidating stock for fast-moving items',
    ], $userId);

    // 2. Zone to Zone Movement Request (Executed)
    $movement2 = $service->requestMovement([
        'company_id' => $company->id,
        'type' => 'zone_to_zone',
        'source_warehouse_id' => $warehouse->id,
        'source_zone_id' => $inventory->warehouse_zone_id,
        'source_bin_id' => $inventory->warehouse_bin_id,
        'destination_warehouse_id' => $warehouse->id,
        'destination_zone_id' => $sourceZone->id, // transferring to A
        'destination_bin_id' => $sourceBin->id,
        'product_id' => $inventory->product_id,
        'quantity' => 1,
        'reason' => 'Routine restructuring',
    ], $userId);

    $service->executeMovement($movement2, $userId);

    echo "Generated Movements:\n";
    echo "1. Pending Bin->Bin: {$movement1->movement_number}\n";
    echo "2. Executed Zone->Zone: {$movement2->movement_number}\n";
    echo "\nNavigate to: /admin/warehouse/movements to test.\n";
});

echo "\nSeeding completed.\n";
