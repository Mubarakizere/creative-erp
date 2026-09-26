<x-layouts.admin title="Chart of Accounts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => route('admin.finance.accounting.ledger.index')],
                ['label' => 'Chart of Accounts']
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Top Hero & Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/10 rounded-xl text-blue-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Chart of Accounts</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Master index of general ledger accounts and hierarchical categories.</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            @can('create', App\Models\ChartOfAccount::class)
                <x-button type="primary" href="{{ route('admin.finance.accounting.chart-of-accounts.create') }}" class="shadow-sm hover:shadow-md transition-shadow w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Account
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Stats Summary Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-stats-card title="Total Accounts" :value="number_format($stats['total'] ?? 0)" color="blue">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Asset Accounts" :value="number_format($stats['assets'] ?? 0)" color="cyan">
            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Liabilities & Equity" :value="number_format(($stats['liabilities'] ?? 0) + ($stats['equity'] ?? 0))" color="amber">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Revenue / Income" :value="number_format($stats['income'] ?? 0)" color="emerald">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Expense Accounts" :value="number_format($stats['expenses'] ?? 0)" color="rose">
            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Filter Toolbar --}}
    <x-card class="mb-6 p-3.5 sm:p-4 bg-white border border-gray-200/80 shadow-sm rounded-xl">
        <form method="GET" action="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" class="flex flex-col gap-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                {{-- Category Pill Tabs --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none max-w-full">
                    @php
                        $currentCat = request('category', 'all');
                        $categories = [
                            'all' => 'All Accounts',
                            'Asset' => 'Assets',
                            'Liability' => 'Liabilities',
                            'Equity' => 'Equity',
                            'Revenue' => 'Revenue',
                            'Expense' => 'Expenses',
                        ];
                    @endphp
                    @foreach($categories as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $key]) }}"
                           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap shrink-0 {{ $currentCat === (string)$key ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Search & Controls --}}
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-72">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search account code, name..." 
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    @if(request()->anyFilled(['search', 'category']))
                        <a href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" 
                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors shrink-0"
                           title="Clear Filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    {{-- Data Table Card --}}
    <x-card class="p-0 border border-gray-200/80 shadow-sm rounded-xl overflow-hidden mb-6">
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Code</th>
                        <th class="py-3.5 px-4">Account Name</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs sm:text-sm">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-blue-50/30 transition-colors group {{ $account->parent_id ? 'bg-gray-50/40' : '' }}">
                            {{-- CODE --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 {{ $account->parent_id ? 'pl-4' : '' }}">
                                    @if($account->parent_id)
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @endif
                                    <span class="font-mono text-xs font-bold {{ $account->parent_id ? 'text-gray-700' : 'text-blue-600' }} px-2 py-0.5 bg-blue-50/60 border border-blue-100 rounded">
                                        {{ $account->code }}
                                    </span>
                                </div>
                            </td>

                            {{-- NAME --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-xs sm:text-sm">
                                        {{ $account->name }}
                                    </span>
                                    @if($account->is_system)
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 rounded-md uppercase tracking-wider" title="System Account">
                                            System
                                        </span>
                                    @endif
                                </div>
                                @if($account->description)
                                    <p class="text-[11px] text-gray-400 mt-0.5 truncate max-w-xs">{{ $account->description }}</p>
                                @endif
                            </td>

                            {{-- TYPE --}}
                            <td class="py-3.5 px-4 text-gray-600 text-xs whitespace-nowrap">
                                {{ $account->accountType->name ?? 'N/A' }}
                            </td>

                            {{-- CATEGORY --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $catName = strtolower($account->accountType->category ?? '');
                                    $catBadge = match(true) {
                                        str_contains($catName, 'asset') => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        str_contains($catName, 'liab') => 'bg-amber-50 text-amber-700 border-amber-200',
                                        str_contains($catName, 'equity') => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        str_contains($catName, 'rev') || str_contains($catName, 'inc') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        str_contains($catName, 'exp') => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 text-[11px] font-semibold border rounded-full {{ $catBadge }}">
                                    {{ $account->accountType->category ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($account->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-action-dropdown>
                                    @can('update', $account)
                                        <x-action-dropdown-item href="{{ route('admin.finance.accounting.chart-of-accounts.edit', $account) }}" icon="edit">
                                            Edit Account
                                        </x-action-dropdown-item>
                                    @endcan

                                    @if(!$account->is_system)
                                        @can('delete', $account)
                                            <x-action-dropdown-item type="button" @click="$dispatch('open-modal', 'delete-account-{{ $account->id }}')" icon="delete" variant="danger">
                                                Delete Account
                                            </x-action-dropdown-item>
                                        @endcan
                                    @endif
                                </x-action-dropdown>

                                @if(!$account->is_system)
                                    @can('delete', $account)
                                        <form action="{{ route('admin.finance.accounting.chart-of-accounts.destroy', $account) }}" method="POST" id="form-delete-account-{{ $account->id }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-confirm-modal
                                            name="delete-account-{{ $account->id }}"
                                            title="Delete Account"
                                            message="Are you sure you want to delete ledger account '{{ $account->code }} - {{ $account->name }}'? This action cannot be undone."
                                            confirmText="Delete Account"
                                            confirmType="danger"
                                        />
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center bg-gray-50/50">
                                <div class="max-w-xs mx-auto text-center px-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No accounts found</h3>
                                    <p class="mt-1 text-xs text-gray-500">No account records match your search or category filter criteria.</p>
                                    @if(request()->anyFilled(['search', 'category']))
                                        <div class="mt-4">
                                            <a href="{{ route('admin.finance.accounting.chart-of-accounts.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                                Clear all filters
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
