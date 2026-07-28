<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'period_date',
        'amount',
        'accumulated_depreciation',
        'book_value',
        'journal_id',
        'status',
        'calculated_by',
        'approved_by',
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
    public function journal() { return $this->belongsTo(Journal::class); }
    public function calculatedBy() { return $this->belongsTo(User::class, 'calculated_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
