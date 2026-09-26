<x-layouts.admin title="Account Types - Administration">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Administration', 'url' => route('admin.dashboard')],
                ['label' => 'Account Types']
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Top Hero & Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/10 rounded-xl text-blue-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Account Types</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Configure financial account classifications for the general ledger and chart of accounts.</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            @can('create', App\Models\AccountType::class)
                <x-button type="primary" href="{{ route('admin.account-types.create') }}" class="shadow-sm hover:shadow-md transition-shadow w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Account Type
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Stats Summary Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-stats-card title="Total Types" :value="number_format($stats['total'] ?? 0)" color="blue">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Asset Types" :value="number_format($stats['assets'] ?? 0)" color="cyan">
            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Liability Types" :value="number_format($stats['liabilities'] ?? 0)" color="amber">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Equity Types" :value="number_format($stats['equity'] ?? 0)" color="indigo">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h10M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Revenue Types" :value="number_format($stats['revenue'] ?? 0)" color="emerald">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </x-stats-card>

        <x-stats-card title="Expense Types" :value="number_format($stats['expense'] ?? 0)" color="rose">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Filter Toolbar --}}
    <x-card class="mb-6 p-3.5 sm:p-4 bg-white border border-gray-200/80 shadow-sm rounded-xl">
        <form method="GET" action="{{ route('admin.account-types.index') }}" class="flex flex-col gap-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                {{-- Category Pill Tabs --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none max-w-full">
                    @php
                        $currentCat = request('category', 'all');
                        $categories = [
                            'all' => 'All Categories',
                            'Asset' => 'Assets',
                            'Liability' => 'Liabilities',
                            'Equity' => 'Equity',
                            'Revenue' => 'Revenue',
                            'Expense' => 'Expenses',
                        ];
                    @endphp
                    @foreach($categories as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $key]) }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap shrink-0 {{ $currentCat === (string)$key ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
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
                               placeholder="Search type name, code..." 
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    @if(request()->anyFilled(['search', 'category']))
                        <a href="{{ route('admin.account-types.index') }}" 
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
                        <th class="py-3.5 px-4">Account Type Name</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Code</th>
                        <th class="py-3.5 px-4 text-center">Linked Accounts</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs sm:text-sm">
                    @forelse($accountTypes as $type)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            {{-- NAME --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-xs sm:text-sm">
                                        {{ $type->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- CATEGORY --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $catName = strtolower($type->category ?? '');
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
                                    {{ $type->category }}
                                </span>
                            </td>

                            {{-- CODE --}}
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs">
                                @if($type->code)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded border border-gray-200 font-bold">
                                        {{ $type->code }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">—</span>
                                @endif
                            </td>

                            {{-- LINKED ACCOUNTS --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $type->chart_of_accounts_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-100 font-semibold' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $type->chart_of_accounts_count }} accounts
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($type->is_active)
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
                                    @can('update', $type)
                                        <x-action-dropdown-item href="{{ route('admin.account-types.edit', $type) }}">
                                            <x-slot:icon>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </x-slot:icon>
                                            Edit Type
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('delete', $type)
                                        @if($type->chart_of_accounts_count === 0)
                                            <x-action-dropdown-item type="button" danger @click="$dispatch('open-modal', 'delete-type-{{ $type->id }}')" icon="delete">
                                                Delete Type
                                            </x-action-dropdown-item>
                                        @else
                                            <button type="button" 
                                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-400 cursor-not-allowed text-left opacity-60" 
                                                    title="Cannot delete because {{ $type->chart_of_accounts_count }} account(s) are linked to this type">
                                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                <span>In Use ({{ $type->chart_of_accounts_count }})</span>
                                            </button>
                                        @endif
                                    @endcan
                                </x-action-dropdown>

                                @can('delete', $type)
                                    @if($type->chart_of_accounts_count === 0)
                                        <form action="{{ route('admin.account-types.destroy', $type) }}" method="POST" id="form-delete-type-{{ $type->id }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-confirm-modal
                                            name="delete-type-{{ $type->id }}"
                                            title="Delete Account Type"
                                            message="Are you sure you want to delete account type '{{ $type->name }}'? This action cannot be undone."
                                            confirmText="Delete Account Type"
                                            confirmType="danger"
                                        />
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center bg-gray-50/50">
                                <div class="max-w-xs mx-auto text-center px-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No account types found</h3>
                                    <p class="mt-1 text-xs text-gray-500">No account type records match your search or category filter criteria.</p>
                                    @if(request()->anyFilled(['search', 'category']))
                                        <div class="mt-4">
                                            <a href="{{ route('admin.account-types.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
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
