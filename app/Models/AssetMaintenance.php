<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'description',
        'vendor',
        'cost',
        'warranty_start',
        'warranty_end',
        'next_maintenance_date',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'warranty_start' => 'date',
        'warranty_end' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
