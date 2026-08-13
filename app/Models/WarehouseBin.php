<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class WarehouseBin extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn, LogsActivity;

    protected $guarded = ['id'];

    public function zone()
    {
        return $this->belongsTo(WarehouseZone::class, 'warehouse_zone_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
