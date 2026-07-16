<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('document.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.view');
    }

    /**
     * Determine whether the user can create models (upload).
     */
    public function create(User $user): bool
    {
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
        return $user->hasPermissionTo('document.download');
    }
    
    /**
     * Determine whether the user can replace the model.
     */
    public function replace(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.replace') || $user->hasPermissionTo('document.update');
    }
}
