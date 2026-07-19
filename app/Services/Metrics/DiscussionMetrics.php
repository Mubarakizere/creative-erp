<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Comment;

class DiscussionMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        $userFirstName = auth()->user()?->first_name ?? '';

        return [
            'total_discussions' => $this->applyFilters(Comment::query(), $filters)->whereNull('parent_id')->count(),
            'comments_today' => $this->applyFilters(Comment::query(), $filters)->whereDate('created_at', now())->count(),
            'my_mentions' => $this->applyFilters(Comment::query(), $filters)->where('body', 'like', '%' . '@' . $userFirstName . '%')->count(),
            'active_threads' => $this->applyFilters(Comment::query(), $filters)->whereNull('parent_id')->has('replies')->count(),
            'internal_notes' => $this->applyFilters(Comment::query(), $filters)->where('is_internal', true)->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $userFirstName = auth()->user()?->first_name ?? '';

        return [
            'recentDiscussions' => $this->applyFilters(Comment::query(), $filters)->with(['commentable', 'user'])->whereNull('parent_id')->latest()->take(5)->get(),
            'latestReplies' => $this->applyFilters(Comment::query(), $filters)->with(['commentable', 'user'])->whereNotNull('parent_id')->latest()->take(5)->get(),
            'myMentions' => $this->applyFilters(Comment::query(), $filters)->with(['commentable', 'user'])->where('body', 'like', '%' . '@' . $userFirstName . '%')->latest()->take(5)->get(),
            'recentlyPinned' => $this->applyFilters(Comment::query(), $filters)->with(['commentable', 'user'])->where('is_pinned', true)->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            // Discussion Summary data
        ];
    }
}
