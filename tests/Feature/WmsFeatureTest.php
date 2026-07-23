<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class WmsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_wms_models_can_be_instantiated()
    {
        $warehouse = new \App\Models\Warehouse();
        $this->assertInstanceOf(\App\Models\Warehouse::class, $warehouse);

        $bin = new \App\Models\WarehouseBin();
        $this->assertInstanceOf(\App\Models\WarehouseBin::class, $bin);

        $task = new \App\Models\WarehouseTask();
        $this->assertInstanceOf(\App\Models\WarehouseTask::class, $task);

        $picking = new \App\Models\WarehousePicking();
        $this->assertInstanceOf(\App\Models\WarehousePicking::class, $picking);

        $packing = new \App\Models\WarehousePacking();
        $this->assertInstanceOf(\App\Models\WarehousePacking::class, $packing);

        $shipment = new \App\Models\WarehouseShipment();
        $this->assertInstanceOf(\App\Models\WarehouseShipment::class, $shipment);

        $movement = new \App\Models\WarehouseMovement();
        $this->assertInstanceOf(\App\Models\WarehouseMovement::class, $movement);

        $return = new \App\Models\WarehouseReturn();
        $this->assertInstanceOf(\App\Models\WarehouseReturn::class, $return);

        $cycleCount = new \App\Models\WarehouseCycleCount();
        $this->assertInstanceOf(\App\Models\WarehouseCycleCount::class, $cycleCount);
    }
    
    public function test_wms_services_can_be_resolved()
    {
        $this->assertInstanceOf(
            \App\Services\Warehouse\PutAwayService::class,
            app(\App\Services\Warehouse\PutAwayService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\PickingService::class,
            app(\App\Services\Warehouse\PickingService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\PackingService::class,
            app(\App\Services\Warehouse\PackingService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\ShippingService::class,
            app(\App\Services\Warehouse\ShippingService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\WarehouseMovementService::class,
            app(\App\Services\Warehouse\WarehouseMovementService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\WarehouseReturnService::class,
            app(\App\Services\Warehouse\WarehouseReturnService::class)
        );
        
        $this->assertInstanceOf(
            \App\Services\Warehouse\CycleCountService::class,
            app(\App\Services\Warehouse\CycleCountService::class)
        );
    }
}
