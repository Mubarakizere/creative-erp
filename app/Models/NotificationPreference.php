<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'database',
        'assignments',
        'mentions',
        'workflow',
        'projects',
        'documents',
        'meetings',
        'system',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'email' => 'boolean',
        'database' => 'boolean',
        'assignments' => 'boolean',
        'mentions' => 'boolean',
        'workflow' => 'boolean',
        'projects' => 'boolean',
        'documents' => 'boolean',
        'meetings' => 'boolean',
        'system' => 'boolean',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
