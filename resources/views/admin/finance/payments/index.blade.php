<x-layouts.admin title="Payments">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Payments']
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Payment::class)
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="p-2.5 bg-emerald-600/10 rounded-xl text-emerald-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Payments</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Track received payments, invoice allocations, and project revenue in RWF.</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @can('create', App\Models\Payment::class)
                    <a href="{{ route('admin.finance.payments.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Record Payment
                    </a>
                @endcan
            </div>
        </div>

        {{-- Executive KPI Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stats-card 
                title="Total Payments" 
                :value="number_format($stats['total_count'] ?? 0)" 
                color="blue"
            >
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="Total Collected (RWF)" 
                :value="'RWF ' . number_format($stats['total_collected'] ?? 0, 2)" 
                color="emerald"
            >
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="This Month (RWF)" 
                :value="'RWF ' . number_format($stats['this_month_collected'] ?? 0, 2)" 
                color="indigo"
            >
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="Average Payment (RWF)" 
                :value="'RWF ' . number_format($stats['avg_payment'] ?? 0, 2)" 
                color="purple"
            >
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
            </x-stats-card>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
            <form method="GET" action="{{ route('admin.finance.payments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                    {{-- Search Input --}}
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Search Keyword</label>
                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Payment #, ref #, client, project, invoice..."
                                   class="block w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    {{-- Project Filter --}}
                    <div>
                        <label for="project_id" class="block text-xs font-semibold text-gray-600 mb-1">Project</label>
                        <select name="project_id" id="project_id" class="block w-full py-2 px-3 text-xs rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ $project->project_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Client Filter --}}
                    <div>
                        <label for="client_id" class="block text-xs font-semibold text-gray-600 mb-1">Client</label>
                        <select name="client_id" id="client_id" class="block w-full py-2 px-3 text-xs rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Method Filter --}}
                    <div>
                        <label for="payment_method_id" class="block text-xs font-semibold text-gray-600 mb-1">Method</label>
                        <select name="payment_method_id" id="payment_method_id" class="block w-full py-2 px-3 text-xs rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">All Methods</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-xs">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'project_id', 'client_id', 'payment_method_id', 'date_from', 'date_to', 'status']))
                            <a href="{{ route('admin.finance.payments.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Additional Filters Row: Date Range --}}
                <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-gray-100 text-xs text-gray-500">
                    <span class="font-semibold text-gray-700">Date Range:</span>
                    <div class="flex items-center gap-2">
                        <label for="date_from" class="text-xs text-gray-500">From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="px-2.5 py-1 text-xs rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="date_to" class="text-xs text-gray-500">To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="px-2.5 py-1 text-xs rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </form>
        </div>

        {{-- Payments Data Table --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/60">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Payment / Ref #</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Client</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Project</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Payment Date</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Method</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Bank Account</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Amount (RWF)</th>
                            <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.finance.payments.show', $payment) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors block">
                                        {{ $payment->reference_number ?? $payment->payment_number }}
                                    </a>
                                    @if($payment->reference_number && $payment->payment_number && $payment->reference_number !== $payment->payment_number)
                                        <span class="text-xs text-gray-400 font-mono">{{ $payment->payment_number }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($payment->client->name ?? 'C', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $payment->client->name ?? 'Unknown Client' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->project)
                                        <div class="flex flex-col">
                                            <a href="{{ route('admin.projects.show', $payment->project) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                                                {{ $payment->project->name }}
                                            </a>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-blue-50 text-blue-700 border border-blue-100 w-max mt-0.5">
                                                {{ $payment->project->project_code }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600 font-medium">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                        {{ $payment->paymentMethod->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->bankAccount)
                                        <div class="text-xs">
                                            <span class="font-medium text-gray-900 block">{{ $payment->bankAccount->bank_name }}</span>
                                            <span class="text-gray-400 font-mono">{{ $payment->bankAccount->account_number }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-emerald-600 tracking-tight">RWF {{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $payment->status ?? 'Completed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-action-dropdown>
                                        @can('view', $payment)
                                            <x-action-dropdown-item href="{{ route('admin.finance.payments.show', $payment) }}" icon="view">
                                                View Receipt
                                            </x-action-dropdown-item>
                                        @endcan

                                        @can('delete', $payment)
                                            <form action="{{ route('admin.finance.payments.destroy', $payment) }}" method="POST" id="delete-payment-form-{{ $payment->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete payment {{ $payment->reference_number ?? $payment->payment_number }}?')) { document.getElementById('delete-payment-form-{{ $payment->id }}').submit(); }" icon="delete" variant="danger">
                                                Delete Payment
                                            </x-action-dropdown-item>
                                        @endcan
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">No payments found</h3>
                                    <p class="text-sm text-gray-500 font-medium">
                                        @if(request()->hasAny(['search', 'project_id', 'client_id', 'payment_method_id', 'date_from', 'date_to', 'status']))
                                            No payments match your filter criteria. Try adjusting or clearing your filters.
                                        @else
                                            Get started by recording a new payment received from a client.
                                        @endif
                                    </p>
                                    <div class="mt-4 flex items-center justify-center gap-3">
                                        @if(request()->hasAny(['search', 'project_id', 'client_id', 'payment_method_id', 'date_from', 'date_to', 'status']))
                                            <a href="{{ route('admin.finance.payments.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                                Clear Filters
                                            </a>
                                        @endif
                                        @can('create', App\Models\Payment::class)
                                            <a href="{{ route('admin.finance.payments.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                Record Payment
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($payments, 'hasPages') && $payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $payments->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view payments.</p>
    </div>
    @endcan
</x-layouts.admin>
