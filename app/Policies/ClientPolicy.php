<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
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

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('customer.view') || $user->hasPermissionTo('client.view') || $user->hasPermissionTo('crm.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $client->company_id && $user->company_id !== $client->company_id) {
            return false;
        }

        return $user->id === $client->account_manager_id || $user->id === $client->created_by || $user->hasPermissionTo('customer.view') || $user->hasPermissionTo('client.view') || $user->hasPermissionTo('crm.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('customer.create') || $user->hasPermissionTo('client.create') || $user->hasPermissionTo('crm.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $client->company_id && $user->company_id !== $client->company_id) {
            return false;
        }

        return $user->id === $client->account_manager_id || $user->id === $client->created_by || $user->hasPermissionTo('customer.update') || $user->hasPermissionTo('client.update') || $user->hasPermissionTo('crm.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $client->company_id && $user->company_id !== $client->company_id) {
            return false;
        }

        return $user->hasPermissionTo('customer.delete') || $user->hasPermissionTo('client.delete') || $user->hasPermissionTo('crm.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $client->company_id && $user->company_id !== $client->company_id) {
            return false;
        }

        return $user->hasPermissionTo('customer.restore') || $user->hasPermissionTo('crm.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can activate the model.
     */
    public function activate(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return $user->hasPermissionTo('customer.activate') || $user->hasPermissionTo('customer.update') || $user->hasPermissionTo('crm.update');
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $user, Client $client): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return $user->hasPermissionTo('customer.deactivate') || $user->hasPermissionTo('customer.update') || $user->hasPermissionTo('crm.update');
    }
}
