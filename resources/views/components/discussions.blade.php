@props(['model'])

<div class="space-y-6" x-data="{ replyingTo: null }">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">Discussions</h3>
    </div>

    {{-- New Comment Form --}}
    @can('create', App\Models\Comment::class)
        <form action="{{ route('admin.comments.store') }}" method="POST" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2 p-6">
            @csrf
            <input type="hidden" name="commentable_type" value="{{ get_class($model) }}">
            <input type="hidden" name="commentable_id" value="{{ $model->id }}">
            <input type="hidden" name="parent_id" x-bind:value="replyingTo">

            <div x-show="replyingTo !== null" class="mb-4 p-3 bg-gray-50 rounded-md border border-gray-200 flex items-center justify-between" style="display: none;">
                <span class="text-sm text-gray-600">Replying to a comment...</span>
                <button type="button" @click="replyingTo = null" class="text-sm text-red-600 hover:text-red-800">Cancel Reply</button>
            </div>

            <div>
                <label for="body" class="sr-only">Comment</label>
                <textarea id="body" name="body" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="Add to the discussion... Use @ to mention someone." required></textarea>
            </div>
            
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @can('internal', App\Models\Comment::class)
                    <div class="flex items-center">
                        <input id="is_internal" name="is_internal" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        <label for="is_internal" class="ml-2 block text-sm text-gray-900">Internal Note</label>
                    </div>
                    @endcan
                </div>
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Post Comment</button>
            </div>
        </form>
    @endcan

    {{-- Comments List --}}
    <div class="space-y-4">
        @foreach($model->comments()->whereNull('parent_id')->orderByDesc('is_pinned')->latest()->get() as $comment)
            @if(!$comment->is_internal || auth()->user()->hasPermissionTo('comment.internal'))
                <x-comment-item :comment="$comment" />
            @endif
        @endforeach
        
        @if($model->comments()->count() === 0)
            <div class="text-center py-6 bg-gray-50 rounded-xl border border-gray-200 border-dashed">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No discussions</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new discussion.</p>
            </div>
        @endif
    </div>
</div>
