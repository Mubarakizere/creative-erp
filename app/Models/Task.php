<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class Task extends Model
{
    use CompanyScoped, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'project_id',
        'parent_id',
        'assigned_to',
        'task_code',
        'name',
        'description',
        'priority',
        'status',
        'progress',
        'actual_material_cost',
        'start_date',
        'due_date',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'progress' => 'integer',
        'actual_material_cost' => 'decimal:2',
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

    public function getTitleAttribute(): string
    {
        return $this->name ?? '';
    }

    public function scopeAccessibleBy($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $query;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('assigned_to', $user->id)
              ->orWhereHas('project', function ($pQuery) use ($user) {
                  $pQuery->accessibleBy($user);
              });
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function milestones(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Milestone::class, 'milestone_task')->withTimestamps();
    }

    /**
     * Get all of the task's documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get all of the task's comments.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all of the task's time entries.
     */
    public function timeEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get all of the task's material requests.
     */
    public function materialRequests(): HasMany
    {
        return $this->hasMany(ProjectMaterialRequest::class);
    }

    /**
     * Get all of the task's material issues.
     */
    public function materialIssues(): HasMany
    {
        return $this->hasMany(ProjectMaterialIssue::class);
    }
}
