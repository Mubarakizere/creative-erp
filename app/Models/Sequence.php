<?php

namespace App\Models;

use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sequence extends Model
{
    use HasFactory, CompanyScoped;

    protected $fillable = [
        'company_id',
        'document_type',
        'prefix',
        'next_number',
        'padding',
        'active',
    ];

    protected $casts = [
        'next_number' => 'integer',
        'padding' => 'integer',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
