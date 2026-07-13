<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'client_id',
        'project_manager_id',
        'project_code',
        'name',
        'description',
        'category',
        'priority',
        'status',
        'progress',
        'estimated_budget',
        'actual_budget',
        'estimated_cost',
        'actual_cost',
        'currency',
        'start_date',
        'planned_end_date',
        'actual_end_date',
        'contract_number',
        'reference_number',
        'location',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_end_date' => 'date',
        'estimated_budget' => 'decimal:2',
        'actual_budget' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress' => 'integer',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('project_role', 'allocation_percentage', 'status')
                    ->withTimestamps();
    }
}
