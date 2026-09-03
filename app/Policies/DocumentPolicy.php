<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
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

    protected function resolveProject(mixed $arg1, mixed $arg2): ?\App\Models\Project
    {
        if ($arg1 instanceof \App\Models\Project) {
            return $arg1;
        }
        if ($arg2 instanceof \App\Models\Project) {
            return $arg2;
        }
        if ($arg1 instanceof Document && $arg1->documentable instanceof \App\Models\Project) {
            return $arg1->documentable;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'document.view');
        }
        return $user->hasPermissionTo('document.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        if ($document->documentable instanceof \App\Models\Project) {
            return $document->documentable->hasPermissionForUser($user, 'document.view');
        }

        if (!$user->hasPermissionTo('document.view')) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return true;
    }

    /**
     * Determine whether the user can create models (upload).
     */
    public function create(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'document.create') || $project->hasPermissionForUser($user, 'document.upload');
        }
        return $user->hasPermissionTo('document.create') || $user->hasPermissionTo('document.upload');
    }

    /**
     * Determine whether the user can update the model (metadata).
     */
    public function update(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.delete');
    }
    
    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Document $document): bool
    {
        if (!$user->hasPermissionTo('document.download') && !$user->hasPermissionTo('document.view')) {
            return false;
        }

        return $this->view($user, $document);
    }
    
    /**
     * Determine whether the user can replace the model.
     */
    public function replace(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.replace') || $user->hasPermissionTo('document.update');
    }
}
