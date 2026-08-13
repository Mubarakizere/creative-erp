<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WarehouseMovement extends Model
{
    use HasUuidColumn, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
        'quantity' => 'decimal:2',
    ];

    public function sourceWarehouse() { return $this->belongsTo(Warehouse::class, 'source_warehouse_id'); }
    public function destinationWarehouse() { return $this->belongsTo(Warehouse::class, 'destination_warehouse_id'); }
    public function sourceZone() { return $this->belongsTo(WarehouseZone::class, 'source_zone_id'); }
    public function destinationZone() { return $this->belongsTo(WarehouseZone::class, 'destination_zone_id'); }
    public function sourceBin() { return $this->belongsTo(WarehouseBin::class, 'source_bin_id'); }
    public function destinationBin() { return $this->belongsTo(WarehouseBin::class, 'destination_bin_id'); }
    public function product() { return $this->belongsTo(Product::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
