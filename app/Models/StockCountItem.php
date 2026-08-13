<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;

class StockCountItem extends Model
{
    use HasUuidColumn;

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
