<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuotationApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'approval_id',
        'status',
        'comments',
        'acted_by',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function workflowApproval()
    {
        return $this->belongsTo(Approval::class, 'approval_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
