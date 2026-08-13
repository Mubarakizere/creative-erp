<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\HasUuidColumn;

use App\Traits\LogsActivity;

class WarehousePacking extends Model
{
    use HasUuidColumn, LogsActivity;

    protected $guarded = ['id'];
    
    public function picking()
    {
        return $this->belongsTo(WarehousePicking::class, 'warehouse_picking_id');
    }
}
