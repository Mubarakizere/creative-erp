<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('comment.view');
    }

    public function view(User $user, Comment $comment): bool
    {
        if ($comment->is_internal && !$user->hasPermissionTo('comment.internal')) {
            return false;
        }
        return $user->hasPermissionTo('comment.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('comment.create');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasPermissionTo('comment.update');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasPermissionTo('comment.delete');
    }

    public function restore(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo('comment.restore');
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo('comment.delete');
    }

    public function pin(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo('comment.pin');
    }

    public function reply(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo('comment.reply');
    }
}
