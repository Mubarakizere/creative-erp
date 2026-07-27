<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class WarehouseBin extends Model
{
    use HasFactory, SoftDeletes, HasUuids, LogsActivity;

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
