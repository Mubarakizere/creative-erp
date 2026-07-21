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
         class="fixed inset-0 bg-gray-500 bg-opacity-25 backdrop-blur-sm transition-opacity"
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
             class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
            
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text"
                       x-ref="searchInput"
                       x-model="query"
                       @input.debounce.300ms="search()"
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                       placeholder="Search projects, tasks, time entries..."
                       role="combobox"
                       aria-expanded="false"
                       aria-controls="options">
                
                {{-- Loading Spinner --}}
                <div x-show="isLoading" class="absolute right-4 top-3.5">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Results --}}
            <ul class="max-h-96 scroll-py-3 overflow-y-auto p-3" id="options" role="listbox" x-show="hasResults()">
                {{-- Projects --}}
                <template x-if="results.projects && results.projects.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Projects</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.projects" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Tasks --}}
                <template x-if="results.tasks && results.tasks.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Tasks</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.tasks" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Time Entries --}}
                <template x-if="results.time_entries && results.time_entries.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Time Entries</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.time_entries" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>
                {{-- Leads --}}
                <template x-if="results.leads && results.leads.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Leads</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.leads" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Contacts --}}
                <template x-if="results.contacts && results.contacts.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Contacts</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.contacts" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Accounts --}}
                <template x-if="results.accounts && results.accounts.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Accounts</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.accounts" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Quotations --}}
                <template x-if="results.quotations && results.quotations.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Quotations</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.quotations" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Invoices --}}
                <template x-if="results.invoices && results.invoices.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Invoices</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.invoices" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Receipts --}}
                <template x-if="results.receipts && results.receipts.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Receipts</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.receipts" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Payments --}}
                <template x-if="results.payments && results.payments.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Payments</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.payments" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>

                {{-- Clients --}}
                <template x-if="results.clients && results.clients.length > 0">
                    <li class="mb-2">
                        <h2 class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-900 uppercase">Clients</h2>
                        <ul class="mt-1 text-sm text-gray-700">
                            <template x-for="item in results.clients" :key="item.id">
                                <li class="group cursor-default select-none rounded-md px-3 py-2 hover:bg-blue-600 hover:text-white" @click="goTo(item.url)">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 flex-none text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <div class="flex flex-col">
                                            <span x-text="item.title"></span>
                                            <span class="text-xs text-gray-500 group-hover:text-blue-100" x-text="item.subtitle"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </li>
                </template>
            </ul>

            {{-- Empty State --}}
            <div x-show="query !== '' && !hasResults() && !isLoading" class="px-6 py-14 text-center text-sm sm:px-14">
                <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-4 font-semibold text-gray-900">No results found</p>
                <p class="mt-2 text-gray-500">We couldn't find anything with that term. Please try again.</p>
            </div>
            
            <div x-show="query === ''" class="px-6 py-14 text-center text-sm sm:px-14">
                <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <p class="mt-4 font-semibold text-gray-900">Search</p>
                <p class="mt-2 text-gray-500">Start typing to search across the workspace.</p>
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
            results: {
                projects: [],
                tasks: [],
                time_entries: [],
                leads: [],
                contacts: [],
                accounts: [],
                quotations: [],
                invoices: [],
                receipts: [],
                payments: [],
                clients: []
            },
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
                this.results = { projects: [], tasks: [], time_entries: [], leads: [], contacts: [], accounts: [], quotations: [], invoices: [], receipts: [], payments: [], clients: [] };
            },
            hasResults() {
                return (this.results.projects && this.results.projects.length > 0) || 
                       (this.results.tasks && this.results.tasks.length > 0) || 
                       (this.results.time_entries && this.results.time_entries.length > 0) ||
                       (this.results.leads && this.results.leads.length > 0) ||
                       (this.results.contacts && this.results.contacts.length > 0) ||
                       (this.results.accounts && this.results.accounts.length > 0) ||
                       (this.results.quotations && this.results.quotations.length > 0) ||
                       (this.results.invoices && this.results.invoices.length > 0) ||
                       (this.results.receipts && this.results.receipts.length > 0) ||
                       (this.results.payments && this.results.payments.length > 0) ||
                       (this.results.clients && this.results.clients.length > 0);
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
