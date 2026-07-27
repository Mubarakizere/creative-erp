<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\WarehouseBin;
use App\Models\WarehouseZone;
use Illuminate\Support\Facades\DB;
use App\Services\Warehouse\CycleCountService;
use App\Models\WarehouseCycleCount;

echo "Seeding Cycle Counts Scenario...\n";

$company = Company::first();
if (!$company) {
    die("No company found.\n");
}

$warehouse = Warehouse::where('company_id', $company->id)->first();
if (!$warehouse) {
    die("No warehouse found.\n");
}

$product = Product::where('company_id', $company->id)->first();
if (!$product) {
    die("Please seed some products first.\n");
}

$zone = WarehouseZone::firstOrCreate([
    'company_id' => $company->id,
    'warehouse_id' => $warehouse->id,
    'name' => 'Storage Zone',
    'type' => 'storage',
]);

$bin = WarehouseBin::firstOrCreate([
    'company_id' => $company->id,
    'warehouse_id' => $warehouse->id,
    'warehouse_zone_id' => $zone->id,
    'code' => 'CC-01',
    'capacity' => 100,
]);

// Seed some inventory
$inventory = Inventory::firstOrCreate(
    [
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'warehouse_bin_id' => $bin->id,
        'product_id' => $product->id,
    ],
    [
        'available_quantity' => 100,
        'valuation_method' => 'FIFO',
        'unit_cost' => 10.00,
    ]
);

$userId = User::where('company_id', $company->id)->first()->id;

DB::transaction(function () use ($company, $warehouse, $userId) {
    $service = app(CycleCountService::class);

    // 1. Pending Cycle Count
    $count1 = $service->initiateCount([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'type' => 'manual',
    ], $userId);

    // 2. Cycle Count with Variance Detected
    $count2 = $service->initiateCount([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'type' => 'monthly',
    ], $userId);
    
    // Simulate someone doing the count with a variance
    $itemsToCount = [];
    $inventories = Inventory::where('warehouse_id', $warehouse->id)->get();
    foreach ($inventories as $inv) {
        $itemsToCount[] = [
            'inventory_id' => $inv->id,
            'counted_quantity' => $inv->available_quantity - 5, // Missing 5 items
        ];
    }
    
    $service->recordCount($count2, $itemsToCount, $userId);

    echo "Generated Cycle Counts:\n";
    echo "1. Pending Count: {$count1->count_number}\n";
    echo "2. Count requiring approval (Variance): {$count2->count_number}\n";
    echo "\nNavigate to: /admin/warehouse/cycle-counts to test.\n";
});

echo "\nSeeding completed.\n";
