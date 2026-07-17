<?php

namespace App\Traits;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Approvable
{
    /**
     * Get the active approval instance for this model.
     * We assume one active approval at a time based on business rules.
     */
    public function approval(): MorphOne
    {
        return $this->morphOne(Approval::class, 'approvable')->latestOfMany();
    }

    /**
     * Get all approvals history for this model.
     */
    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    /**
     * Get the current approval status.
     */
    public function getApprovalStatusAttribute(): string
    {
        return $this->approval ? $this->approval->status : 'Draft';
    }
}
