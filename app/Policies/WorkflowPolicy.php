<?php

namespace App\Policies;

use App\Models\ApprovalWorkflow;
use App\Models\User;

class WorkflowPolicy
{
    /**
     * Determine whether the user can view any models.
     */
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
