<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CostType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($costType) {
            if (empty($costType->slug)) {
                $costType->slug = Str::slug($costType->name, '_');
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function budgetLines()
    {
        return $this->hasMany(BudgetLine::class, 'cost_type', 'slug');
    }
}
