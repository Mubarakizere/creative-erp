<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\WarehouseBin;
use App\Models\WarehouseZone;
use Illuminate\Support\Facades\DB;
use App\Services\Warehouse\WarehouseReturnService;

echo "Seeding Returns Scenario...\n";

$company = Company::first();
if (!$company) {
    die("No company found.\n");
}

$warehouse = Warehouse::where('company_id', $company->id)->first();
if (!$warehouse) {
    die("No warehouse found.\n");
}

$product1 = Product::where('company_id', $company->id)->first();
$product2 = Product::where('company_id', $company->id)->skip(1)->first();

if (!$product1 || !$product2) {
    die("Please seed some products first.\n");
}

$userId = User::where('company_id', $company->id)->first()->id;

DB::transaction(function () use ($company, $warehouse, $product1, $product2, $userId) {
    $service = app(WarehouseReturnService::class);

    // 1. Customer Return (Pending)
    $return1 = $service->logReturn([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'type' => 'customer_return',
        'requires_accounting_adjustment' => false,
    ], $userId);

    $return1->update([
        'items' => [
            [
                'product_id' => $product1->id,
                'quantity' => 2,
                'unit_cost' => $product1->cost_price ?? 5.00,
            ],
            [
                'product_id' => $product2->id,
                'quantity' => 1,
                'unit_cost' => $product2->cost_price ?? 10.00,
            ]
        ]
    ]);

    // 2. Damaged Stock (Pending, requires accounting adjustment)
    $return2 = $service->logReturn([
        'company_id' => $company->id,
        'warehouse_id' => $warehouse->id,
        'type' => 'damaged_stock',
        'requires_accounting_adjustment' => true,
    ], $userId);

    $return2->update([
        'items' => [
            [
                'product_id' => $product1->id,
                'quantity' => 5,
                'unit_cost' => $product1->cost_price ?? 5.00,
            ]
        ]
    ]);

    echo "Generated Returns:\n";
    echo "1. Pending Customer Return: {$return1->return_number}\n";
    echo "2. Pending Damaged Stock: {$return2->return_number}\n";
    echo "\nNavigate to: /admin/warehouse/returns to test.\n";
});

echo "\nSeeding completed.\n";
