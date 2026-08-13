<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WarehouseShipment extends Model
{
    use HasUuidColumn, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function packings()
    {
        return $this->hasMany(WarehousePacking::class, 'warehouse_shipment_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
