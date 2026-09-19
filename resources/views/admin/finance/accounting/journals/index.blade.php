<x-layouts.admin title="Journal Entries">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => route('admin.finance.accounting.ledger.index')],
                ['label' => 'Journal Entries']
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Top Hero & Action Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/10 rounded-xl text-blue-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Journal Entries</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Manage manual and automated general ledger adjustments and transactions.</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            @can('create', App\Models\Journal::class)
                <x-button type="primary" href="{{ route('admin.finance.accounting.journals.create') }}" class="shadow-sm hover:shadow-md transition-shadow w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Journal Entry
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Stats Summary Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-stats-card 
            title="Total Journal Entries" 
            :value="number_format($stats['total'] ?? 0)" 
            color="blue"
        >
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Posted Entries" 
            :value="number_format($stats['posted'] ?? 0)" 
            color="emerald"
        >
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Draft Entries" 
            :value="number_format($stats['draft'] ?? 0)" 
            color="amber"
        >
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Total Volume Posted" 
            :value="'RWF ' . number_format($stats['total_volume'] ?? 0, 2)" 
            color="indigo"
        >
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Filter & Search Toolbar --}}
    <x-card class="mb-6 p-3.5 sm:p-4 bg-white border border-gray-200/80 shadow-sm rounded-xl">
        <form method="GET" action="{{ route('admin.finance.accounting.journals.index') }}" class="flex flex-col gap-3.5">
            {{-- Top Filter Row: Status Pills & Search --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                {{-- Status Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none max-w-full">
                    @php
                        $currentStatus = request('status', 'all');
                        $statuses = [
                            'all' => 'All Entries',
                            'Draft' => 'Draft',
                            'Pending' => 'Pending',
                            'Posted' => 'Posted',
                            'Voided' => 'Voided',
                        ];
                    @endphp
                    @foreach($statuses as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => 1]) }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap shrink-0 {{ $currentStatus === (string)$key ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Search & Additional Controls --}}
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-72">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search Journal #, Memo, Ref..." 
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.finance.accounting.journals.index') }}" 
                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors shrink-0"
                           title="Clear Filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Date Range Controls --}}
            <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs">
                <span class="font-medium text-gray-500 shrink-0">Date Range:</span>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}" 
                           class="px-2.5 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 flex-1 sm:flex-none">
                    <span class="text-gray-400">to</span>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}" 
                           class="px-2.5 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 flex-1 sm:flex-none">
                    <button type="submit" class="px-3.5 py-1.5 bg-gray-900 text-white font-semibold rounded-md hover:bg-gray-800 transition-colors w-full sm:w-auto">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Data Table Card --}}
    <x-card class="p-0 border border-gray-200/80 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-3 sm:px-4">Journal #</th>
                        <th class="py-3.5 px-3 sm:px-4 whitespace-nowrap">Date</th>
                        <th class="py-3.5 px-3 sm:px-4">Memo</th>
                        <th class="py-3.5 px-3 sm:px-4">Reference</th>
                        <th class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">Debit / Credit</th>
                        <th class="py-3.5 px-3 sm:px-4 text-center">Status</th>
                        <th class="py-3.5 px-3 sm:px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs sm:text-sm">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
                                <a href="{{ route('admin.finance.accounting.journals.show', $journal) }}" 
                                   class="font-mono text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $journal->journal_number ?? 'JE-' . str_pad($journal->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-gray-700 font-medium text-xs whitespace-nowrap">
                                {{ $journal->date ? $journal->date->format('M d, Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-gray-600 text-xs max-w-xs truncate" title="{{ $journal->memo }}">
                                {{ \Illuminate\Support\Str::limit($journal->memo, 45) }}
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-gray-500 text-xs font-mono whitespace-nowrap">
                                {{ $journal->reference_number ?: '-' }}
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-gray-900">
                                    RWF {{ number_format($journal->total_debit ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-center whitespace-nowrap">
                                @php
                                    $statusConfig = match($journal->status) {
                                        'Draft' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                                        'Pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-400'],
                                        'Posted' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                        'Voided' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
                                        default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusConfig['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ $journal->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">
                                <x-action-dropdown>
                                    @can('view', $journal)
                                        <x-action-dropdown-item href="{{ route('admin.finance.accounting.journals.show', $journal) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @if($journal->status === 'Draft' || $journal->status === 'Pending')
                                        @can('post', $journal)
                                            <form action="{{ route('admin.finance.accounting.journals.post', $journal) }}" method="POST" id="post-journal-form-{{ $journal->id }}">
                                                @csrf
                                            </form>
                                            <x-action-dropdown-item onclick="document.getElementById('post-journal-form-{{ $journal->id }}').submit()">
                                                Post to Ledger
                                            </x-action-dropdown-item>
                                        @endcan
                                    @endif

                                    @can('delete', $journal)
                                        <form action="{{ route('admin.finance.accounting.journals.destroy', $journal) }}" method="POST" id="delete-journal-form-{{ $journal->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this draft journal entry?')) document.getElementById('delete-journal-form-{{ $journal->id }}').submit()" icon="delete" variant="danger">
                                            Delete Entry
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center bg-gray-50/50">
                                <div class="max-w-xs mx-auto text-center px-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No journal entries found</h3>
                                    <p class="mt-1 text-xs text-gray-500">No records match your selected search or filter criteria.</p>
                                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                                        <div class="mt-4">
                                            <a href="{{ route('admin.finance.accounting.journals.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
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
        
        @if($journals->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $journals->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
