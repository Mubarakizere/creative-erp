<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class InventoryReservation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuidColumn;

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
    public function product() { return $this->belongsTo(Product::class); }
    public function reference() { return $this->morphTo(); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function zone() { return $this->belongsTo(WarehouseZone::class, 'zone_id'); }
}