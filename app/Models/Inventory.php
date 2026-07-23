<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    public function product() { return $this->belongsTo(Product::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function zone() { return $this->belongsTo(WarehouseZone::class, 'warehouse_zone_id'); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }
}