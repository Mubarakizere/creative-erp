<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class InventoryReservation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
    public function product() { return $this->belongsTo(Product::class); }
    public function reference() { return $this->morphTo(); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function zone() { return $this->belongsTo(WarehouseZone::class, 'zone_id'); }
}