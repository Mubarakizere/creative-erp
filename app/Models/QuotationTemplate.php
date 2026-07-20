<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class QuotationTemplate extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $fillable = [
        'company_id',
        'name',
        'header_text',
        'footer_text',
        'default_payment_term_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function defaultPaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'default_payment_term_id');
    }
}
