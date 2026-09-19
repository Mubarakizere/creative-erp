<x-layouts.admin title="Documentation Search Results">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Help Center
            </a>
        </div>

        {{-- Search Header --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-1">Search Results</h1>
            <p class="text-xs text-gray-500 mb-4">Showing guides matching "<span class="font-semibold text-gray-900">{{ $query }}</span>"</p>

            <form action="{{ route('admin.documentation.search') }}" method="GET" class="relative">
                <div class="relative flex items-center">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}" 
                           class="w-full pl-11 pr-24 py-3 text-xs bg-gray-50 text-gray-900 rounded-xl border border-gray-300 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           required>
                    <button type="submit" class="absolute right-2 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm">
                        Search Again
                    </button>
                </div>
            </form>
        </div>

        {{-- Results List --}}
        <div class="space-y-4">
            @if($articles->count() > 0)
                @foreach($articles as $article)
                    <a href="{{ route('admin.documentation.show', [$article->category->slug, $article->slug]) }}" 
                       class="block bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 hover:border-blue-300 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 mb-1.5">
                            <span class="px-2.5 py-0.5 bg-blue-50 border border-blue-100 rounded-full">
                                {{ $article->category->name }}
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                            {{ $article->title }}
                        </h2>
                        <p class="text-gray-600 text-xs leading-relaxed line-clamp-3">
                            {{ Str::limit(strip_tags(Str::markdown($article->content ?? '')), 220) }}
                        </p>
                    </a>
                @endforeach
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/80 shadow-sm">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">No matching articles found</h3>
                    <p class="mt-1 text-xs text-gray-500">We couldn't find anything matching "{{ $query }}". Try searching for "Journal Entries", "Invoicing", or "Procurement".</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
