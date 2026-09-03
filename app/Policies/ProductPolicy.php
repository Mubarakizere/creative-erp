<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

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

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('product.view') || $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, Product $product)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $product->company_id && $user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.view') || $user->hasPermissionTo('inventory.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('product.create') || $user->hasPermissionTo('inventory.create');
    }

    public function update(User $user, Product $product)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $product->company_id && $user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.update') || $user->hasPermissionTo('inventory.update');
    }

    public function delete(User $user, Product $product)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $product->company_id && $user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.delete') || $user->hasPermissionTo('inventory.delete');
    }
}
