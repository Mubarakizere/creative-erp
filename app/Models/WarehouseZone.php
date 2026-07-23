<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseZone extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }
}