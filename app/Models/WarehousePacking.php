<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Traits\LogsActivity;

class WarehousePacking extends Model
{
    use HasUuids, LogsActivity;

    protected $guarded = ['id'];
    
    public function picking()
    {
        return $this->belongsTo(WarehousePicking::class, 'warehouse_picking_id');
    }
}
