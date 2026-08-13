<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WarehouseReturn extends Model
{
    use HasUuidColumn, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'inspected_at' => 'datetime',
        'requires_accounting_adjustment' => 'boolean',
        'items' => 'array',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function returnable()
    {
        return $this->morphTo();
    }
}
