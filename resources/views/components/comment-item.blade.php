@props(['comment', 'depth' => 0])

<div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-4 {{ $depth > 0 ? 'ml-8 border-l-4 border-blue-500 mt-2' : '' }} {{ $comment->is_pinned ? 'ring-2 ring-yellow-400 bg-yellow-50' : '' }}">
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-3">
            @if($comment->user->avatar)
                <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->first_name }}" class="h-8 w-8 rounded-full bg-gray-50">
            @else
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                    {{ $comment->user->initials }}
                </div>
            @endif
            <div>
                <div class="text-sm font-medium text-gray-900">
                    {{ $comment->user->full_name }}
                    @if($comment->is_internal)
                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 ml-2">Internal Note</span>
                    @endif
                    @if($comment->is_pinned)
                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20 ml-2">
                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                            Pinned
                        </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }} @if($comment->edited_at) (edited) @endif</div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            @can('reply', $comment)
                <button type="button" @click="replyingTo = {{ $comment->id }}; $el.closest('.space-y-6').querySelector('textarea').focus(); window.scrollTo({ top: $el.closest('.space-y-6').querySelector('form').offsetTop, behavior: 'smooth' })" class="text-xs text-gray-500 hover:text-blue-600 font-medium">Reply</button>
            @endcan
            
            @can('pin', $comment)
                <form action="{{ $comment->is_pinned ? route('admin.comments.unpin', $comment) : route('admin.comments.pin', $comment) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-yellow-600 font-medium">{{ $comment->is_pinned ? 'Unpin' : 'Pin' }}</button>
                </form>
            @endcan

            @can('delete', $comment)
                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="mt-3 text-sm text-gray-700 prose max-w-none">
        {!! preg_replace('/@([a-zA-Z0-9_]+)/', '<span class="font-semibold text-blue-600 bg-blue-50 px-1 rounded">@$1</span>', nl2br(e($comment->body))) !!}
    </div>

    {{-- Replies --}}
    @if($comment->replies->count() > 0)
        <div class="mt-4 border-l-2 border-gray-100 pl-4 space-y-4">
            @foreach($comment->replies as $reply)
                @if(!$reply->is_internal || auth()->user()->hasPermissionTo('comment.internal'))
                    <x-comment-item :comment="$reply" :depth="$depth + 1" />
                @endif
            @endforeach
        </div>
    @endif
</div>
