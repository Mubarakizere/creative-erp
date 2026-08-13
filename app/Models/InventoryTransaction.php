<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuidColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory, SoftDeletes, HasUuidColumn;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'datetime',
    ];
    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function reference() { return $this->morphTo(); }

    public function getReferenceCodeAttribute()
    {
        if (!$this->relationLoaded('reference')) {
            $this->load('reference');
        }
        $ref = $this->reference;
        if (!$ref) return '—';

        return $ref->code ?? 
               $ref->receipt_number ?? 
               $ref->movement_number ?? 
               $ref->return_number ?? 
               $ref->shipment_number ?? 
               $ref->picking_number ?? 
               $ref->packing_number ?? 
               $ref->count_number ?? 
               $ref->issue_number ?? 
               $ref->tracking_number ?? 
               '—';
    }
}