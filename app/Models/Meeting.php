<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Meeting types.
     */
    public const TYPE_INTERNAL = 'internal';
    public const TYPE_CLIENT = 'client';
    public const TYPE_PROJECT = 'project';
    public const TYPE_HR = 'hr';
    public const TYPE_TRAINING = 'training';
    public const TYPE_SALES = 'sales';
    public const TYPE_OTHER = 'other';

    /**
     * Meeting statuses.
     */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RESCHEDULED = 'rescheduled';

    /**
     * Attendee statuses.
     */
    public const ATTENDANCE_PENDING = 'pending';
    public const ATTENDANCE_ACCEPTED = 'accepted';
    public const ATTENDANCE_DECLINED = 'declined';
    public const ATTENDANCE_TENTATIVE = 'tentative';

    protected $fillable = [
        'company_id',
        'branch_id',
        'project_id',
        'title',
        'description',
        'meeting_type',
        'location',
        'meeting_link',
        'start_at',
        'end_at',
        'timezone',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the attendees for the meeting.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_attendees')
                    ->withPivot('attendance_status', 'response_at')
                    ->withTimestamps();
    }

    /**
     * Get all of the meeting's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to upcoming meetings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>=', now())
                     ->where('status', '!=', self::STATUS_CANCELLED)
                     ->orderBy('start_at');
    }

    /**
     * Scope to today's meetings.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_at', now()->toDateString())
                     ->where('status', '!=', self::STATUS_CANCELLED)
                     ->orderBy('start_at');
    }

    /**
     * Scope to meetings for a specific user (as attendee or organizer).
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhereHas('attendees', function ($q2) use ($userId) {
                  $q2->where('users.id', $userId);
              });
        });
    }

    /**
     * Scope by meeting type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('meeting_type', $type);
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope meetings within a date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_at', [$start, $end])
              ->orWhereBetween('end_at', [$start, $end])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->where('start_at', '<=', $start)
                     ->where('end_at', '>=', $end);
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the given user is the organizer.
     */
    public function isOrganizer(User $user): bool
    {
        return $this->created_by === $user->id;
    }

    /**
     * Check if the meeting is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if the meeting is in the past.
     */
    public function isPast(): bool
    {
        return $this->end_at->isPast();
    }

    /**
     * Check if the meeting is currently ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->start_at->isPast() && $this->end_at->isFuture();
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationMinutesAttribute(): int
    {
        return (int) $this->start_at->diffInMinutes($this->end_at);
    }

    /**
     * Get a formatted duration string.
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes;
        if ($minutes < 60) {
            return "{$minutes}min";
        }
        $hours = floor($minutes / 60);
        $remaining = $minutes % 60;
        return $remaining > 0 ? "{$hours}h {$remaining}min" : "{$hours}h";
    }

    /**
     * Get available meeting types.
     */
    public static function getMeetingTypes(): array
    {
        return [
            self::TYPE_INTERNAL => 'Internal',
            self::TYPE_CLIENT => 'Client Meeting',
            self::TYPE_PROJECT => 'Project Meeting',
            self::TYPE_HR => 'HR Meeting',
            self::TYPE_TRAINING => 'Training',
            self::TYPE_SALES => 'Sales Meeting',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_RESCHEDULED => 'Rescheduled',
        ];
    }

    /**
     * Get available attendance statuses.
     */
    public static function getAttendanceStatuses(): array
    {
        return [
            self::ATTENDANCE_PENDING => 'Pending',
            self::ATTENDANCE_ACCEPTED => 'Accepted',
            self::ATTENDANCE_DECLINED => 'Declined',
            self::ATTENDANCE_TENTATIVE => 'Tentative',
        ];
    }
}
