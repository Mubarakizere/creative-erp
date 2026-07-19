<?php

namespace App\Policies;

use App\Models\ReportTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('report.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReportTemplate $reportTemplate): bool
    {
        if (!$user->hasPermissionTo('report.view')) {
            return false;
        }

        // System templates are available to all who can view reports
        if ($reportTemplate->is_system) {
            return true;
        }

        // Cross-company check
        if ($reportTemplate->company_id !== null && $reportTemplate->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('report.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReportTemplate $reportTemplate): bool
    {
        if (!$user->hasPermissionTo('report.create')) { 
            return false;
        }

        if ($reportTemplate->is_system) {
            return false;
        }

        return $reportTemplate->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReportTemplate $reportTemplate): bool
    {
        if (!$user->hasPermissionTo('report.delete')) {
            return false;
        }

        if ($reportTemplate->is_system) {
            return false;
        }

        return $reportTemplate->created_by === $user->id;
    }

    /**
     * Determine whether the user can export reports.
     */
    public function export(User $user, ReportTemplate $reportTemplate): bool
    {
        return $user->hasPermissionTo('report.export') && $this->view($user, $reportTemplate);
    }
}
