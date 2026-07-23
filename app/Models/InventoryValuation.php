<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryValuation extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}