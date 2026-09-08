<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class ProjectExpense extends Model
{
    use CompanyScoped, HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'branch_id',
        'project_id',
        'user_id',
        'category',
        'title',
        'description',
        'amount',
        'currency',
        'expense_date',
        'vendor_name',
        'payment_status',
        'payment_method',
        'receipt_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeLabor($query)
    {
        return $query->whereIn('category', ['Worker Salary', 'Labor', 'Payroll']);
    }

    public function scopeDirectExpenses($query)
    {
        return $query->whereNotIn('category', ['Worker Salary', 'Labor', 'Payroll']);
    }
}
