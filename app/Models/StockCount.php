<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockCount extends Model
{
    use HasUuidColumn, SoftDeletes;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(StockCountItem::class);
    }
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}