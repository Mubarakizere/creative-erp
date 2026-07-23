<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class InventoryAdjustment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
    ];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function transactions() { return $this->morphMany(InventoryTransaction::class, 'reference'); }
    public function approval() { return $this->morphOne(\App\Models\Approval::class, 'approvable'); }
}