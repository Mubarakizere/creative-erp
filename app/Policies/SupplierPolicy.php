<?php
namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('supplier.view'); }
    public function view(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.view') && $user->company_id === $supplier->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('supplier.create'); }
    public function update(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.update') && $user->company_id === $supplier->company_id; }
    public function delete(User $user, Supplier $supplier) { return $user->hasPermissionTo('supplier.delete') && $user->company_id === $supplier->company_id; }
}