<x-layouts.admin title="Help Center">
<div class="flex flex-col md:flex-row gap-6">
    <!-- Sidebar Navigation -->
    <div class="w-full md:w-64 lg:w-72 flex-shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Help Center
                </a>
            </div>
            
            <div class="max-h-[calc(100vh-12rem)] overflow-y-auto p-4 custom-scrollbar">
                @foreach($categories as $cat)
                    <div class="mb-4 last:mb-0">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            {{ $cat->name }}
                        </h3>
                        <ul class="space-y-1">
                            @foreach($cat->articles as $art)
                                <li>
                                    <a href="{{ route('admin.documentation.show', [$cat->slug, $art->slug]) }}" 
                                       class="block px-2 py-1.5 text-sm rounded-lg transition-colors duration-150 {{ $art->id === $article->id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                        {{ $art->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center hover:text-blue-600">
                            Help Center
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span class="ml-1 md:ml-2 text-gray-500">{{ $category->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Title & Actions -->
            <div class="flex justify-between items-start mb-8 pb-6 border-b border-gray-100">
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $article->title }}</h1>
                @can('documentation.update')
                    <a href="{{ route('admin.documentation-articles.edit', $article) }}" class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                @endcan
            </div>

            <!-- Content (Markdown converted to HTML) -->
            <div class="prose prose-blue prose-lg max-w-none text-gray-600 prose-headings:text-gray-900 prose-headings:font-bold prose-a:text-blue-600 prose-img:rounded-xl prose-img:shadow-sm">
                {!! $content !!}
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-100">
                <p class="text-sm text-gray-400">Last updated: {{ $article->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>
</x-layouts.admin>

