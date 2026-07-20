<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class PaymentTerm extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $fillable = [
        'company_id',
        'name',
        'days',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'days' => 'integer',
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
}
