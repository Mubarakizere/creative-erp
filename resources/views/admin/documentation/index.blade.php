<x-layouts.admin title="Help Center & Knowledge Base">
    <div class="space-y-8">
        {{-- Hero Header & Search Section --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 rounded-2xl p-6 sm:p-10 text-white shadow-xl">
            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/20 border border-blue-400/30 rounded-full text-blue-200 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Knowledge Base & Guides
                </div>
                
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white">
                    How can we help you today?
                </h1>
                <p class="mt-2 text-sm sm:text-base text-blue-100/80 leading-relaxed">
                    Explore user guides, accounting workflows, procurement procedures, and ERP troubleshooting.
                </p>

                {{-- Hero Search Form --}}
                <form action="{{ route('admin.documentation.search') }}" method="GET" class="mt-6 sm:mt-8 relative max-w-2xl">
                    <div class="relative flex items-center">
                        <svg class="w-5 h-5 text-gray-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q" 
                               class="w-full pl-11 pr-24 py-3.5 text-sm bg-white text-gray-900 rounded-xl shadow-lg focus:ring-4 focus:ring-blue-400/50 focus:outline-none transition-all placeholder-gray-400" 
                               placeholder="Search for articles (e.g. Journal Entries, Invoicing, Procurement)..." 
                               required>
                        <button type="submit" class="absolute right-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            {{-- Background Accent Pattern --}}
            <div class="absolute -right-12 -bottom-12 opacity-10 pointer-events-none">
                <svg class="w-96 h-96 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        {{-- Admin Management Bar --}}
        @can('documentation.create')
            <div class="flex items-center justify-between p-4 bg-white border border-gray-200/80 rounded-xl shadow-sm">
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                    Documentation Authoring Portal
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.documentation-categories.index') }}" class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Manage Categories
                    </a>
                    <a href="{{ route('admin.documentation-articles.index') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Manage Articles
                    </a>
                </div>
            </div>
        @endcan

        {{-- Categories Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="p-6 flex-1">
                        {{-- Category Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0 border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                    @if($category->icon)
                                        {!! $category->icon !!}
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $category->name }}
                                    </h2>
                                    <span class="text-xs text-gray-400 font-medium">
                                        {{ $category->articles->count() }} {{ \Illuminate\Support\Str::plural('article', $category->articles->count()) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Articles List --}}
                        <ul class="space-y-2 mt-4 pt-4 border-t border-gray-100">
                            @foreach($category->articles->take(6) as $article)
                                <li>
                                    <a href="{{ route('admin.documentation.show', [$category->slug, $article->slug]) }}" 
                                       class="text-xs text-gray-600 hover:text-blue-600 hover:font-semibold flex items-center gap-2 group/item py-1 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover/item:text-blue-600 group-hover/item:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        <span class="truncate">{{ $article->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if($category->articles->count() > 6)
                        <div class="px-6 py-3 bg-gray-50/80 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center justify-between">
                            <span>+ {{ $category->articles->count() - 6 }} more guides</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/80 shadow-sm">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">No documentation available</h3>
                <p class="mt-1 text-xs text-gray-500">The help center is currently being updated with new guides.</p>
            </div>
        @endif
    </div>
</x-layouts.admin>
