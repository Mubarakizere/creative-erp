<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Department extends Model
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'company_id',
        'branch_id',
        'name',
        'code',
        'manager_name',
        'email',
        'phone',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the model and auto-generate UUID on creation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Department $department) {
            if (empty($department->uuid)) {
                $department->uuid = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the department is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the department is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the company that owns this department.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch that owns this department.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Future Relationships (prepared for upcoming sprints)
    |--------------------------------------------------------------------------
    */

    // public function users(): HasMany
    // {
    //     return $this->hasMany(User::class);
    // }

    // public function employees(): HasMany
    // {
    //     return $this->hasMany(Employee::class);
    // }

    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }
}
