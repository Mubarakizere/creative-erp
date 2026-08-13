<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseZone extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn;

    protected $guarded = ['id'];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }
    public function bins() { return $this->hasMany(WarehouseBin::class); }
}