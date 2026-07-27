<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\WarehouseShipment;
use App\Models\WarehouseTask;
use App\Services\Warehouse\PickingService;
use App\Services\Warehouse\PackingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "Seeding Packing Scenario...\n";

$company = Company::first();
if (!$company) {
    die("No company found. Please seed the database first.\n");
}

$warehouse = Warehouse::where('company_id', $company->id)->first();
if (!$warehouse) {
    die("No warehouse found.\n");
}

$inventories = Inventory::where('company_id', $company->id)
    ->where('warehouse_id', $warehouse->id)
    ->whereNotNull('warehouse_bin_id')
    ->where('available_quantity', '>', 0)
    ->take(3)
    ->get();

if ($inventories->isEmpty()) {
    die("No bin inventory found. Please run php seed-picks.php first to generate dummy inventory.\n");
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
        $mockItem->quantity = min(5, $inv->available_quantity); 
        $items->push($mockItem);
    }

    $shipment->setAttribute('items', $items);

    // Generate Picking Request
    $pickingService = app(PickingService::class);
    $picking = $pickingService->generatePicking($company->id, $warehouse->id, $shipment);

    // Completely Pick everything automatically so it's ready for packing!
    $tasks = WarehouseTask::where('taskable_type', \App\Models\WarehousePicking::class)
        ->where('taskable_id', $picking->id)
        ->get();

    $userId = \App\Models\User::first()->id;

    foreach ($tasks as $task) {
        $pickingService->completePickTask($task, $userId);
    }
    
    // Refresh picking to ensure it's marked as completed
    $picking->refresh();
    
    echo "Completed Picking Task generated successfully!\n";
    echo "Picking Number: " . $picking->picking_number . "\n";
    echo "Status: " . $picking->status . "\n\n";

    // Now, automatically create ONE packing task so it shows in 'Packings in Progress'
    $packingService = app(PackingService::class);
    $packing = $packingService->createPackingFromPicking($picking, $userId);
    
    echo "Started a Packing process automatically!\n";
    echo "Packing Number: " . $packing->packing_number . "\n";
});

echo "Seeding completed. Please visit /admin/warehouse/packing\n";
