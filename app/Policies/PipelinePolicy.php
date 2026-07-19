<?php

namespace App\Policies;

use App\Models\Pipeline;
use App\Models\User;

class PipelinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.pipeline');
    }

    public function view(User $user, Pipeline $pipeline): bool
    {
        if ($user->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.pipeline');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.manage');
    }

    public function update(User $user, Pipeline $pipeline): bool
    {
        if ($user->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function delete(User $user, Pipeline $pipeline): bool
    {
        if ($user->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }
        
        if ($pipeline->is_default) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
