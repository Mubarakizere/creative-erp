<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'datetime',
    ];
    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function reference() { return $this->morphTo(); }
}