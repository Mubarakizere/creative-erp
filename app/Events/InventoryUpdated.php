<?php

namespace App\Events;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inventory;
    public $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Inventory $inventory, InventoryTransaction $transaction)
    {
        $this->inventory = $inventory;
        $this->transaction = $transaction;
    }
}
