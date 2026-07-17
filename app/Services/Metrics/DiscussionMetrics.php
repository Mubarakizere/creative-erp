<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Comment;

class DiscussionMetrics implements MetricProvider
{
    public function cards(): array
    {
        $userFirstName = auth()->user()?->first_name ?? '';

        return [
            'total_discussions' => Comment::whereNull('parent_id')->count(),
            'comments_today' => Comment::whereDate('created_at', now())->count(),
            'my_mentions' => Comment::where('body', 'like', '%' . '@' . $userFirstName . '%')->count(),
            'active_threads' => Comment::whereNull('parent_id')->has('replies')->count(),
            'internal_notes' => Comment::where('is_internal', true)->count(),
        ];
    }

    public function widgets(): array
    {
        $userFirstName = auth()->user()?->first_name ?? '';

        return [
            'recentDiscussions' => Comment::with(['commentable', 'user'])->whereNull('parent_id')->latest()->take(5)->get(),
            'latestReplies' => Comment::with(['commentable', 'user'])->whereNotNull('parent_id')->latest()->take(5)->get(),
            'myMentions' => Comment::with(['commentable', 'user'])->where('body', 'like', '%' . '@' . $userFirstName . '%')->latest()->take(5)->get(),
            'recentlyPinned' => Comment::with(['commentable', 'user'])->where('is_pinned', true)->latest()->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [
            // Discussion Summary data
        ];
    }
}
