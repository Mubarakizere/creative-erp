<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => 'boolean',
    ];
    public function manager() { return $this->belongsTo(User::class, 'manager_id'); }
    public function zones() { return $this->hasMany(WarehouseZone::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}