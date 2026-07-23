<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'attributes' => 'array',
        'track_inventory' => 'boolean',
    ];
    public function product() { return $this->belongsTo(Product::class); }
    public function barcodes() { return $this->hasMany(Barcode::class); }
}