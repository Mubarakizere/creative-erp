<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Traits\LogsActivity;

class WarehousePicking extends Model
{
    use HasUuids, LogsActivity;
    
    protected $guarded = ['id'];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function packings()
    {
        return $this->hasMany(WarehousePacking::class, 'warehouse_picking_id');
    }
}
