<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\LogsActivity;

class InventoryTransfer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
    ];

    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse() { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function fromZone() { return $this->belongsTo(WarehouseZone::class, 'from_zone_id'); }
    public function toZone() { return $this->belongsTo(WarehouseZone::class, 'to_zone_id'); }
    public function transactions() { return $this->morphMany(InventoryTransaction::class, 'reference'); }
    public function approval() { return $this->morphOne(\App\Models\Approval::class, 'approvable'); }
}