<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseShipment;
use App\Services\Warehouse\PickingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "Seeding Picking Tasks...\n";

$company = Company::first();
if (!$company) {
    die("No company found. Please seed the database first.\n");
}

$warehouse = Warehouse::where('company_id', $company->id)->first();
if (!$warehouse) {
    die("No warehouse found.\n");
}

// Find some inventory that is sitting in bins
$inventories = Inventory::where('company_id', $company->id)
    ->where('warehouse_id', $warehouse->id)
    ->whereNotNull('warehouse_bin_id')
    ->where('available_quantity', '>', 0)
    ->take(3)
    ->get();

if ($inventories->isEmpty()) {
    echo "No bin inventory found. Generating dummy bin inventory...\n";
    $bin = WarehouseBin::where('warehouse_id', $warehouse->id)->first();
    if (!$bin) {
        $zone = \App\Models\WarehouseZone::firstOrCreate([
            'warehouse_id' => $warehouse->id,
            'name' => 'Storage Zone',
            'code' => 'Z-STOR',
            'type' => 'storage',
            'company_id' => $company->id
        ]);
        $bin = WarehouseBin::create([
            'company_id' => $company->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'code' => 'BIN-999',
            'status' => 'active',
            'max_weight' => 1000,
            'max_volume' => 1000,
        ]);
    }
    
    $product = \App\Models\Product::first();
    
    Inventory::create([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'warehouse_bin_id' => $bin->id,
        'product_id' => $product->id,
        'available_quantity' => 100,
        'unit_cost' => 10.00
    ]);
    
    $bin->update(['current_quantity' => 100]);
    
    $inventories = Inventory::where('company_id', $company->id)
        ->where('warehouse_id', $warehouse->id)
        ->whereNotNull('warehouse_bin_id')
        ->where('available_quantity', '>', 0)
        ->take(3)
        ->get();
}

DB::transaction(function () use ($company, $warehouse, $inventories) {
    // Mock a WarehouseShipment
    $shipment = WarehouseShipment::create([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'shipment_number' => 'SHP-' . strtoupper(uniqid()),
        'status' => 'pending',
    ]);

    // Create a mock collection of items based on available inventory
    $items = collect();
    foreach ($inventories as $inv) {
        $mockItem = new stdClass();
        $mockItem->product_id = $inv->product_id;
        // Request a quantity less than or equal to what's in the bin
        $mockItem->quantity = min(5, $inv->available_quantity); 
        $items->push($mockItem);
        echo "Added item to shipment: Product ID {$mockItem->product_id}, Qty {$mockItem->quantity}\n";
    }

    $shipment->setAttribute('items', $items);

    // Generate Picking Request
    $pickingService = app(PickingService::class);
    $picking = $pickingService->generatePicking($company->id, $warehouse->id, $shipment);

    echo "Picking task generated successfully!\n";
    echo "Pick List Number: " . $picking->picking_number . "\n";
});

echo "Seeding completed.\n";
