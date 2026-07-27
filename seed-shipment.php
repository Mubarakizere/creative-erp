<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\WarehousePacking;
use App\Models\WarehousePicking;
use App\Models\WarehouseShipment;
use App\Models\WarehouseTask;
use App\Services\Warehouse\PackingService;
use App\Services\Warehouse\PickingService;
use Illuminate\Support\Facades\DB;

echo "Seeding Shipment Scenario...\n";

$company = Company::first();
if (!$company) {
    die("No company found.\n");
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
    die("No bin inventory found. Run php seed-picks.php first.\n");
}

DB::transaction(function () use ($company, $warehouse, $inventories) {
    $userId = \App\Models\User::first()->id;

    // 1. Create a shipment (the pickable source)
    $shipment = WarehouseShipment::create([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'shipment_number' => 'SHP-SEED-' . strtoupper(substr(uniqid(), -6)),
        'status' => 'pending',
        'created_by' => $userId,
    ]);

    // 2. Generate a Picking from mock items
    $items = collect();
    foreach ($inventories as $inv) {
        $mockItem = new stdClass();
        $mockItem->product_id = $inv->product_id;
        $mockItem->quantity = min(3, $inv->available_quantity);
        $items->push($mockItem);
    }
    $shipment->setAttribute('items', $items);

    $pickingService = app(PickingService::class);
    $picking = $pickingService->generatePicking($company->id, $warehouse->id, $shipment);

    // 3. Complete picking
    $tasks = WarehouseTask::where('taskable_type', WarehousePicking::class)
        ->where('taskable_id', $picking->id)
        ->get();
    foreach ($tasks as $task) {
        $pickingService->completePickTask($task, $userId);
    }
    $picking->refresh();
    echo "Picking {$picking->picking_number} completed.\n";

    // 4. Create a packing and complete it
    $packingService = app(PackingService::class);
    $packing = $packingService->createPackingFromPicking($picking, $userId);
    $packingService->completePacking($packing, [
        'total_weight' => 12.5,
        'length' => 30,
        'width' => 20,
        'height' => 15,
        'notes' => 'Standard carton box',
    ], $userId);
    $packing->refresh();
    echo "Packing {$packing->packing_number} completed.\n";

    // 5. Attach the packing to the shipment
    $packing->update(['warehouse_shipment_id' => $shipment->id]);

    echo "\nShipment ready for workflow testing!\n";
    echo "Shipment Number: {$shipment->shipment_number}\n";
    echo "Status: {$shipment->status}\n";
    echo "Navigate to: /admin/warehouse/shipments\n";
});

echo "\nSeeding completed.\n";
