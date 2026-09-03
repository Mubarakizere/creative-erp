<div x-data="globalSearch()"
     x-show="isOpen"
     @keydown.escape.window="close()"
     @open-search.window="open()"
     @keydown.window.prevent.ctrl.k="open()"
     @keydown.window.prevent.cmd.k="open()"
     class="relative z-50"
     style="display: none;"
     role="dialog"
     aria-modal="true">
    
    {{-- Backdrop Overlay --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
         @click="close()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.away="close()"
             class="mx-auto max-w-2xl transform divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5 transition-all">
            
            {{-- Search Bar Input --}}
            <div class="relative group flex items-center px-4">
                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-ref="searchInput"
                       x-model="query"
                       @input.debounce.250ms="search()"
                       class="h-14 w-full border-0 bg-transparent pl-3 pr-8 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-base outline-none font-medium"
                       placeholder="Search projects, tasks, assets, clients, documents..."
                       role="combobox">
                
                {{-- Loading Indicator --}}
                <div x-show="isLoading" class="absolute right-4">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Quick Jump Shortcuts when query is empty --}}
            <div x-show="query === ''" class="p-4 bg-slate-50/50">
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 px-2">Quick Shortcuts</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-semibold">
                    <a href="{{ route('admin.projects.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:shadow-xs transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Projects</span>
                    </a>
                    <a href="{{ route('admin.projects.team.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:shadow-xs transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Teams</span>
                    </a>
                    <a href="{{ route('admin.assets.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:shadow-xs transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        <span>Equipment</span>
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:shadow-xs transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Documents</span>
                    </a>
                </div>
            </div>

            {{-- Dynamic Results --}}
            <ul class="max-h-[60vh] scroll-py-3 overflow-y-auto p-2 divide-y divide-slate-100" id="options" role="listbox" x-show="hasResults()">
                <template x-for="(items, category) in results" :key="category">
                    <template x-if="items && items.length > 0">
                        <li class="py-2 first:pt-0 last:pb-0">
                            <h2 class="px-3 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest" x-text="category.replace(/_/g, ' ')"></h2>
                            <ul class="mt-1 space-y-1 text-sm text-slate-700">
                                <template x-for="item in items" :key="item.id">
                                    <li class="group cursor-pointer select-none rounded-xl px-3 py-2.5 hover:bg-blue-50/70 transition-all flex items-center justify-between" @click="goTo(item.url)">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-blue-100 text-slate-500 group-hover:text-blue-600 flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-bold text-slate-900 group-hover:text-blue-700 truncate transition-colors text-xs" x-text="item.title"></span>
                                                <span class="text-[11px] text-slate-500 truncate mt-0.5" x-text="item.subtitle"></span>
                                            </div>
                                        </div>
                                        <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </li>
                                </template>
                            </ul>
                        </li>
                    </template>
                </template>
            </ul>

            {{-- Empty Search Results --}}
            <div x-show="query !== '' && !hasResults() && !isLoading" class="px-6 py-12 text-center text-sm">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <p class="font-bold text-slate-900 text-sm">No records found</p>
                <p class="mt-1 text-xs text-slate-500">No matches found for "<span x-text="query" class="font-bold text-slate-700"></span>".</p>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-4 py-2.5 bg-slate-50/80 text-xs text-slate-500 flex items-center justify-between">
                <span>Press <kbd class="px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-600 bg-white rounded border border-slate-200">Esc</kbd> to exit</span>
                <span>Type to search</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('globalSearch', () => ({
            isOpen: false,
            query: '',
            isLoading: false,
            results: {},
            open() {
                this.isOpen = true;
                setTimeout(() => this.$refs.searchInput.focus(), 100);
            },
            close() {
                this.isOpen = false;
                this.query = '';
                this.clearResults();
            },
            clearResults() {
                this.results = {};
            },
            hasResults() {
                return Object.values(this.results).some(arr => arr && arr.length > 0);
            },
            async search() {
                if (this.query.length < 2) {
                    this.clearResults();
                    return;
                }
                
                this.isLoading = true;
                
                try {
                    const response = await fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await response.json();
                    this.results = data;
                } catch (error) {
                    console.error('Search failed', error);
                } finally {
                    this.isLoading = false;
                }
            },
            goTo(url) {
                window.location.href = url;
            }
        }));
    });
</script>
