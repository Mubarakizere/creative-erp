<x-layouts.admin title="Help Center">
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Creative Century Engineering Help Center</h1>
            <p class="text-sm text-gray-500 mt-1">Learn how to use Creative Century Engineering and find answers to common questions.</p>
        </div>
        @can('documentation.create')
            <div class="flex gap-2">
                <a href="{{ route('admin.documentation-categories.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Manage Categories
                </a>
                <a href="{{ route('admin.documentation-articles.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Manage Articles
                </a>
            </div>
        @endcan
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.documentation.search') }}" method="GET" class="relative max-w-3xl mx-auto">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="q" class="block w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out text-lg" placeholder="Search documentation, workflows, troubleshooting..." required>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        @if($category->icon)
                            <div class="text-blue-600">
                                {!! $category->icon !!}
                            </div>
                        @else
                            <div class="text-blue-600">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                        <h2 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h2>
                    </div>
                    
                    <ul class="space-y-3">
                        @foreach($category->articles->take(5) as $article)
                            <li>
                                <a href="{{ route('admin.documentation.show', [$category->slug, $article->slug]) }}" class="text-sm text-gray-600 hover:text-blue-600 hover:underline flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="line-clamp-2">{{ $article->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                @if($category->articles->count() > 5)
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                        <span class="text-sm text-gray-500">+ {{ $category->articles->count() - 5 }} more articles</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 border-dashed">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No documentation available</h3>
            <p class="mt-1 text-sm text-gray-500">The help center is currently being populated.</p>
        </div>
    @endif
</div>
</x-layouts.admin>

