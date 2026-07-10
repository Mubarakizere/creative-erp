<?php

namespace App\Models;

use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Branch extends Model
{
    /** @use HasFactory<BranchFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'code',
        'email',
        'phone',
        'manager_name',
        'country',
        'state',
        'city',
        'address',
        'postal_code',
        'latitude',
        'longitude',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

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

        static::creating(function (Branch $branch) {
            if (empty($branch->uuid)) {
                $branch->uuid = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the branch is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the branch is inactive.
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
     * Get the company that owns this branch.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    // public function departments(): HasMany
    // {
    //     return $this->hasMany(Department::class);
    // }

    // public function employees(): HasMany
    // {
    //     return $this->hasMany(Employee::class);
    // }

    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }

    // public function warehouses(): HasMany
    // {
    //     return $this->hasMany(Warehouse::class);
    // }
}
