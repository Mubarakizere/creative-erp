<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuids, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'track_inventory' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'serial_numbers' => 'boolean',
        'batch_numbers' => 'boolean',
        'expiration_date' => 'boolean',
    ];
    public function category() { return $this->belongsTo(ProductCategory::class, 'product_category_id'); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function unit() { return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id'); }
    public function tax() { return $this->belongsTo(Tax::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function barcodes() { return $this->hasMany(Barcode::class); }
    public function supplierProducts() { return $this->hasMany(SupplierProduct::class); }
    public function inventory() { return $this->hasMany(Inventory::class); }
    public function inventoryReservations() { return $this->hasMany(InventoryReservation::class); }
    public function inventoryTransactions() { return $this->hasManyThrough(InventoryTransaction::class, Inventory::class); }
}