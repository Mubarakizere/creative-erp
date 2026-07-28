<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetImpairment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'date',
        'reason',
        'amount',
        'status',
        'journal_id',
        'requested_by',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function journal() { return $this->belongsTo(Journal::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
