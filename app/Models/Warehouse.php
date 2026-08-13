<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\CompanyScoped;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn, CompanyScoped;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => 'boolean',
    ];
    public function manager() { return $this->belongsTo(User::class, 'manager_id'); }
    public function zones() { return $this->hasMany(WarehouseZone::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}