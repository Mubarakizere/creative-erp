<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Traits\CompanyScoped;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use CompanyScoped, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'company_id',
        'branch_id',
        'client_type',
        'company_name',
        'first_name',
        'last_name',
        'display_name',
        'email',
        'phone',
        'alternate_phone',
        'website',
        'tax_number',
        'registration_number',
        'country',
        'state',
        'city',
        'address',
        'postal_code',
        'logo',
        'status',
        'notes',
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

        static::creating(function (Client $client) {
            if (empty($client->uuid)) {
                $client->uuid = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the client logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Future Relationships
    |--------------------------------------------------------------------------
    */

    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }

    // public function contacts(): HasMany
    // {
    //     return $this->hasMany(Contact::class);
    // }

    /**
     * Get all of the client's documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // public function quotations(): HasMany
    // {
    //     return $this->hasMany(Quotation::class);
    // }
}
