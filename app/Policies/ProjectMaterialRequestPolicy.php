<?php

namespace App\Policies;

use App\Models\ProjectMaterialRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectMaterialRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('material_request.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id && $user->company_id !== $request->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('material_request.view')) {
            return false;
        }

        if ($request->project && !$request->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('material_request.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id && $user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Draft') {
            return false;
        }

        if (!$user->hasPermissionTo('material_request.update')) {
            return false;
        }

        if ($request->project && !$request->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id && $user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Draft') {
            return false;
        }

        if (!$user->hasPermissionTo('material_request.delete')) {
            return false;
        }

        if ($request->project && !$request->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can submit the model.
     */
    public function submit(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id && $user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Draft' && $request->status !== 'Rejected') {
            return false;
        }

        if (!$user->hasPermissionTo('material_request.submit') && !$user->hasPermissionTo('material_request.update')) {
            return false;
        }

        if ($request->project && !$request->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Submitted' && $request->status !== 'Under Review') {
            return false;
        }

        return $user->hasPermissionTo('material_request.approve');
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Submitted' && $request->status !== 'Under Review') {
            return false;
        }

        return $user->hasPermissionTo('material_request.reject') || $user->hasPermissionTo('material_request.approve');
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status === 'Approved') {
            return false;
        }

        return $user->hasPermissionTo('material_request.update');
    }

    /**
     * Determine whether the user can convert the model to procurement.
     */
    public function convertToProcurement(User $user, ProjectMaterialRequest $request): bool
    {
        if ($user->company_id !== $request->company_id) {
            return false;
        }

        if ($request->status !== 'Approved') {
            return false;
        }

        return $user->hasPermissionTo('material_request.convert_to_procurement');
    }
}
