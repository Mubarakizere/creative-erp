<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StockCountItem extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    public function stockCount()
    {
        return $this->belongsTo(StockCount::class);
    }
    
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
