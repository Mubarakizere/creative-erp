<?php

namespace App\Policies;

use App\Models\ApprovalWorkflow;
use App\Models\User;

class WorkflowPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('workflow.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        return $user->hasPermissionTo('workflow.view') && 
               ($user->hasRole('Super Admin') || $approvalWorkflow->company_id === $user->company_id || $approvalWorkflow->company_id === null);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('workflow.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        return $user->hasPermissionTo('workflow.update') && 
               ($user->hasRole('Super Admin') || $approvalWorkflow->company_id === $user->company_id || $approvalWorkflow->company_id === null);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApprovalWorkflow $approvalWorkflow): bool
    {
        return $user->hasPermissionTo('workflow.delete') && 
               ($user->hasRole('Super Admin') || $approvalWorkflow->company_id === $user->company_id);
    }
}
