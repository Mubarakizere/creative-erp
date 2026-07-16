<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

class CommentController extends Controller
{
    protected \App\Services\CommentService $commentService;

    public function __construct(\App\Services\CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(StoreCommentRequest $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create', Comment::class);

        $data = $request->validated();
        
        $type = $data['commentable_type'];
        if (!class_exists($type)) {
            $type = '\\App\\Models\\' . class_basename($type);
        }
        
        $commentable = $type::findOrFail($data['commentable_id']);
        
        $this->commentService->create($commentable, $data, auth()->id());

        return back()->with('success', 'Comment posted successfully.');
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $comment);

        $this->commentService->update($comment, $request->validated(), auth()->id());

        return back()->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $comment);

        $this->commentService->delete($comment);

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function restore($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('restore', $comment);

        $this->commentService->restore($comment);

        return back()->with('success', 'Comment restored successfully.');
    }

    public function pin(Comment $comment)
    {
        \Illuminate\Support\Facades\Gate::authorize('pin', $comment);

        $this->commentService->pin($comment);

        return back()->with('success', 'Comment pinned successfully.');
    }

    public function unpin(Comment $comment)
    {
        \Illuminate\Support\Facades\Gate::authorize('pin', $comment);

        $this->commentService->unpin($comment);

        return back()->with('success', 'Comment unpinned successfully.');
    }
}
