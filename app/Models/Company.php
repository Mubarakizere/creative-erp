<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'legal_name',
        'slug',
        'email',
        'phone',
        'alternate_phone',
        'website',
        'logo',
        'favicon',
        'registration_number',
        'tax_number',
        'country',
        'state',
        'city',
        'address',
        'postal_code',
        'currency',
        'timezone',
        'language',
        'working_days',
        'working_hours_start',
        'working_hours_end',
        'notes',
        'status',
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
            'working_days' => 'array',
            'working_hours_start' => 'datetime:H:i',
            'working_hours_end' => 'datetime:H:i',
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

        static::creating(function (Company $company) {
            if (empty($company->uuid)) {
                $company->uuid = (string) Str::uuid();
            }

            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the company logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Get the company favicon URL.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if (! $this->favicon) {
            return null;
        }

        return asset('storage/' . $this->favicon);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the company is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the company is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the company is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the users belonging to this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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

    // public function branches(): HasMany
    // {
    //     return $this->hasMany(Branch::class);
    // }

    // public function departments(): HasMany
    // {
    //     return $this->hasMany(Department::class);
    // }

    // public function clients(): HasMany
    // {
    //     return $this->hasMany(Client::class);
    // }

    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }

    // public function employees(): HasMany
    // {
    //     return $this->hasMany(Employee::class);
    // }

    // public function warehouses(): HasMany
    // {
    //     return $this->hasMany(Warehouse::class);
    // }
}
