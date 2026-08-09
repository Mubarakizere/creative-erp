<x-layouts.admin title="Help Center">
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-8">
        <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Help Center
        </a>
        
        <h1 class="text-2xl font-bold text-gray-900">Search Results</h1>
        <p class="text-sm text-gray-500 mt-1">Showing results for "{{ $query }}"</p>
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="{{ route('admin.documentation.search') }}" method="GET" class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="q" value="{{ $query }}" class="block w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out" required>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                <button type="submit" class="inline-flex items-center px-4 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="space-y-4">
        @if($articles->count() > 0)
            @foreach($articles as $article)
                <a href="{{ route('admin.documentation.show', [$article->category->slug, $article->slug]) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center text-sm text-blue-600 mb-2">
                        <span class="font-medium">{{ $article->category->name }}</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ $article->title }}</h2>
                    <p class="text-gray-600 text-sm line-clamp-2">
                        {{ Str::limit(strip_tags(Str::markdown($article->content ?? '')), 200) }}
                    </p>
                </a>
            @endforeach
        @else
            <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 border-dashed">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-sm text-gray-500">We couldn't find anything matching "{{ $query }}". Please try different keywords.</p>
            </div>
        @endif
    </div>
</div>
</x-layouts.admin>

