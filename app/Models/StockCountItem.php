<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockCountItem extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function stockCount() { return $this->belongsTo(StockCount::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
