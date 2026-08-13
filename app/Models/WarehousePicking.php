<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\HasUuidColumn;

use App\Traits\LogsActivity;

class WarehousePicking extends Model
{
    use HasUuidColumn, LogsActivity;
    
    protected $guarded = ['id'];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function packings()
    {
        return $this->hasMany(WarehousePacking::class, 'warehouse_picking_id');
    }
}
