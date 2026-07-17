<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('approval.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Approval $approval): bool
    {
        if ($user->hasRole('Super Admin')) return true;
        if ($approval->submitted_by === $user->id) return true;
        if ($user->hasPermissionTo('approval.view')) return true;
        
        $currentStep = $approval->currentStep;
        if ($currentStep) {
            if ($currentStep->approver_user_id === $user->id) return true;
            if ($currentStep->role && $user->hasRole($currentStep->role->name)) return true;
        }

        return false;
    }

    /**
     * Determine whether the user can approve.
     */
    public function approve(User $user, Approval $approval): bool
    {
        if ($approval->status !== 'Pending Approval') return false;
        if ($approval->submitted_by === $user->id) return false; // Requesters cannot approve their own requests
        
        $currentStep = $approval->currentStep;
        if (!$currentStep) return false;

        if ($user->hasRole('Super Admin')) return true;

        if ($currentStep->approver_user_id === $user->id) return true;
        if ($currentStep->role && $user->hasRole($currentStep->role->name)) return true;

        return false;
    }

    /**
     * Determine whether the user can reject.
     */
    public function reject(User $user, Approval $approval): bool
    {
        return $this->approve($user, $approval); // Same logic as approve
    }

    /**
     * Determine whether the user can return for revision.
     */
    public function return(User $user, Approval $approval): bool
    {
        return $this->approve($user, $approval);
    }
    
    /**
     * Determine whether the user can resubmit.
     */
    public function submit(User $user, Approval $approval): bool
    {
        return $user->id === $approval->submitted_by && $approval->status === 'Returned for Revision';
    }

    /**
     * Determine whether the user can cancel.
     */
    public function cancel(User $user, Approval $approval): bool
    {
        return $user->id === $approval->submitted_by && $approval->status === 'Pending Approval';
    }
}
