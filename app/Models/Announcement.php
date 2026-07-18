<?php

namespace App\Models;

use App\Models\Traits\CompanyScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes, CompanyScoped;

    protected $fillable = [
        'company_id',
        'title',
        'content',
        'category',
        'priority',
        'audience_type',
        'audience_id',
        'starts_at',
        'expires_at',
        'is_pinned',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
            'is_pinned' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the announcement.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the announcement.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the specific users targeted by this announcement.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where(function ($q) {
                         $q->whereNull('starts_at')
                           ->orWhere('starts_at', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>=', now());
                     });
    }
}
