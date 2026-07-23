<?php

namespace App\Services\Inventory;

use App\Models\Warehouse;
use App\Models\WarehouseZone;

class WarehouseService
{
    public function createWarehouse(array $data)
    {
        return Warehouse::create($data);
    }

    public function updateWarehouse(Warehouse $warehouse, array $data)
    {
        $warehouse->update($data);
        return $warehouse;
    }

    public function createZone(Warehouse $warehouse, array $data)
    {
        return $warehouse->zones()->create($data);
    }
}
