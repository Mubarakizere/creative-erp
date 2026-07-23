<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class StockCount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'variance_detected' => 'boolean',
    ];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(StockCountItem::class); }
}