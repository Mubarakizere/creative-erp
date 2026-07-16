<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use App\Events\CommentCreated;
use App\Events\CommentUpdated;
use App\Events\CommentDeleted;
use App\Events\CommentRestored;
use App\Events\CommentPinned;
use App\Events\CommentUnpinned;
use App\Events\MentionDetected;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function create(Model $commentable, array $data, int $userId): Comment
    {
        $comment = new Comment();
        $comment->body = $this->sanitizeHtml($data['body']);
        $comment->is_internal = $data['is_internal'] ?? false;
        $comment->user_id = $userId;
        $comment->created_by = $userId;
        $comment->parent_id = $data['parent_id'] ?? null;
        
        $commentable->comments()->save($comment);

        event(new CommentCreated($comment));
        
        $this->detectAndNotifyMentions($comment);

        return $comment;
    }

    public function update(Comment $comment, array $data, int $userId): Comment
    {
        $oldBody = $comment->body;
        $comment->body = $this->sanitizeHtml($data['body']);
        $comment->is_internal = $data['is_internal'] ?? $comment->is_internal;
        $comment->edited_at = now();
        $comment->updated_by = $userId;
        $comment->save();

        event(new CommentUpdated($comment, $oldBody));
        
        $this->detectAndNotifyMentions($comment);

        return $comment;
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
        event(new CommentDeleted($comment));
    }

    public function restore(Comment $comment): void
    {
        $comment->restore();
        event(new CommentRestored($comment));
    }

    public function pin(Comment $comment): void
    {
        $comment->is_pinned = true;
        $comment->save();
        event(new CommentPinned($comment));
    }

    public function unpin(Comment $comment): void
    {
        $comment->is_pinned = false;
        $comment->save();
        event(new CommentUnpinned($comment));
    }

    protected function detectAndNotifyMentions(Comment $comment): void
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $comment->body, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);
            
            $users = User::all()->filter(function ($user) use ($usernames) {
                $formattedName = str_replace(' ', '', $user->first_name . $user->last_name);
                return in_array($formattedName, $usernames) || in_array($user->first_name, $usernames);
            });

            foreach ($users as $user) {
                event(new MentionDetected($comment, $user));
            }
        }
    }

    protected function sanitizeHtml(string $html): string
    {
        return strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><a><code><blockquote>');
    }
}
