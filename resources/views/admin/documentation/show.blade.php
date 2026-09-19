<x-layouts.admin title="{{ $article->title }} - Help Center">
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sidebar Navigation --}}
        <div class="w-full lg:w-72 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden sticky top-6">
                <div class="p-4 border-b border-gray-100 bg-gray-50/80 flex items-center justify-between">
                    <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Help Center Index
                    </a>
                </div>
                
                <div class="max-h-[calc(100vh-14rem)] overflow-y-auto p-4 space-y-5 scrollbar-thin">
                    @foreach($categories as $cat)
                        <div>
                            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <span>{{ $cat->name }}</span>
                            </h3>
                            <ul class="space-y-1">
                                @foreach($cat->articles as $art)
                                    <li>
                                        <a href="{{ route('admin.documentation.show', [$cat->slug, $art->slug]) }}" 
                                           class="block px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $art->id === $article->id ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
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

        {{-- Main Article Container --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-10">
                {{-- Breadcrumbs & Category Pill --}}
                <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100 flex-wrap">
                    <nav class="flex text-xs text-gray-500" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 sm:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.documentation.index') }}" class="hover:text-blue-600 font-medium">
                                    Help Center
                                </a>
                            </li>
                            <li class="flex items-center text-gray-400">
                                <svg class="w-3.5 h-3.5 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span>{{ $category->name }}</span>
                            </li>
                        </ol>
                    </nav>

                    @can('documentation.update')
                        <a href="{{ route('admin.documentation-articles.edit', $article) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Guide
                        </a>
                    @endcan
                </div>

                {{-- Article Title & Metadata --}}
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        {{ $article->title }}
                    </h1>
                    <div class="mt-3 flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Updated {{ $article->updated_at->diffForHumans() }}
                        </span>
                        <span>•</span>
                        <span class="text-blue-600 font-semibold">{{ $category->name }}</span>
                    </div>
                </div>

                {{-- Article Markdown Content --}}
                <div class="doc-article-content border-t border-gray-100 pt-6">
                    {!! $content !!}
                </div>
                
                {{-- Footer Navigation --}}
                <div class="mt-12 pt-6 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors">
                        ← Back to Documentation Overview
                    </a>
                    <span class="text-xs text-gray-400">Creative ERP Knowledge Base</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
