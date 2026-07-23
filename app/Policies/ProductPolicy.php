<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('product.view');
    }

    public function view(User $user, Product $product)
    {
        if ($user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('product.create');
    }

    public function update(User $user, Product $product)
    {
        if ($user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.update');
    }

    public function delete(User $user, Product $product)
    {
        if ($user->company_id !== $product->company_id) return false;
        return $user->hasPermissionTo('product.delete');
    }
}
