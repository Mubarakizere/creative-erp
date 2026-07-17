<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends DatabaseNotification
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at',
        'category',
        'priority',
        'icon',
        'color',
        'action_url',
        'action_text',
        'company_id',
        'branch_id',
        'created_by'
    ];

    /**
     * Get the company associated with the notification.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch associated with the notification.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
