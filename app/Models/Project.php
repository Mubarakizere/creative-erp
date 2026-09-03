<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class Project extends Model
{
    use CompanyScoped, HasFactory, SoftDeletes;

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

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Completed', 'Cancelled', 'Closed']);
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
            $q->where('project_manager_id', $user->id)
              ->orWhereHas('projectMembers', function ($sub) use ($user) {
                  $sub->where('user_id', $user->id);
              })
              ->orWhereHas('client', function ($sub) use ($user) {
                  $sub->where('email', $user->email);
              });
        });
    }

    public function isAssignedTo(User $user): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($this->project_manager_id === $user->id) {
            return true;
        }

        if ($this->projectMembers()->where('user_id', $user->id)->exists()) {
            return true;
        }

        if ($this->client_id && $this->client && $this->client->email === $user->email) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user has a specific permission for this project,
     * either through their global system role OR their assigned project_role.
     */
    public function hasPermissionForUser(?User $user, string $permission): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->company_id && $user->company_id && $user->company_id !== $this->company_id) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if (!$this->isAssignedTo($user)) {
            return false;
        }

        // Determine user's assigned role on THIS project
        $projectRoleName = null;
        if ($this->project_manager_id === $user->id) {
            $projectRoleName = 'Project Manager';
        } else {
            $member = $this->relationLoaded('projectMembers')
                ? $this->projectMembers->firstWhere('user_id', $user->id)
                : $this->projectMembers()->where('user_id', $user->id)->where('status', 'Active')->first();
            $projectRoleName = $member?->project_role;
        }

        if ($projectRoleName) {
            if ($projectRoleName === 'Super Admin') {
                return true;
            }

            $spatieRole = \Spatie\Permission\Models\Role::where('name', $projectRoleName)->first();
            if ($spatieRole && $spatieRole->hasPermissionTo($permission)) {
                return true;
            }
        }

        return $user->hasPermissionTo($permission);
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

    public function milestones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all of the project's documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get all of the project's comments.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all of the project's meetings.
     */
    public function meetings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get all of the project's time entries.
     */
    public function timeEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get all of the project's material requests.
     */
    public function materialRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMaterialRequest::class);
    }

    /**
     * Get all of the project's material issues.
     */
    public function materialIssues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMaterialIssue::class);
    }
}
