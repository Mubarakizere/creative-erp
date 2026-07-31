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
    
    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
         @click="close()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="close()"
             class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 transition-all">
            
            <div class="relative group">
                <svg class="pointer-events-none absolute left-4 top-4 h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text"
                       x-ref="searchInput"
                       x-model="query"
                       @input.debounce.300ms="search()"
                       class="h-14 w-full border-0 bg-transparent pl-12 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-base outline-none"
                       placeholder="Search projects, tasks, time entries..."
                       role="combobox"
                       aria-expanded="false"
                       aria-controls="options">
                
                {{-- Loading Spinner --}}
                <div x-show="isLoading" class="absolute right-4 top-4">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Results --}}
            <ul class="max-h-[60vh] scroll-py-3 overflow-y-auto p-2" id="options" role="listbox" x-show="hasResults()">
                <template x-for="(items, category) in results" :key="category">
                    <template x-if="items && items.length > 0">
                        <li class="mb-4 last:mb-0">
                            <h2 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider" x-text="category.replace(/_/g, ' ')"></h2>
                            <ul class="mt-1 text-sm text-gray-700">
                                <template x-for="item in items" :key="item.id">
                                    <li class="group cursor-pointer select-none rounded-xl px-3 py-3 hover:bg-blue-50 transition-colors" @click="goTo(item.url)">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4 text-gray-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col flex-1 min-w-0">
                                                <span class="font-medium text-gray-900 group-hover:text-blue-700 truncate transition-colors" x-text="item.title"></span>
                                                <span class="text-xs text-gray-500 truncate mt-0.5" x-text="item.subtitle"></span>
                                            </div>
                                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </li>
                    </template>
                </template>
            </ul>

            {{-- Empty State --}}
            <div x-show="query !== '' && !hasResults() && !isLoading" class="px-6 py-16 text-center text-sm sm:px-14">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 text-base">No results found</p>
                <p class="mt-2 text-gray-500">We couldn't find anything matching "<span x-text="query" class="font-medium text-gray-700"></span>".</p>
            </div>
            
            {{-- Initial State --}}
            <div x-show="query === ''" class="px-6 py-16 text-center text-sm sm:px-14">
                <div class="mx-auto w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 text-base">Global Search</p>
                <p class="mt-2 text-gray-500">Start typing to quickly find projects, tasks, or colleagues.</p>
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
