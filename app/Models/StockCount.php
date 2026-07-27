<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockCount extends Model
{
    use HasUuids, SoftDeletes;

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