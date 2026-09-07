<x-layouts.admin title="Request for Quotations">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'RFQs & Supplier Quotations'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\SupplierQuotation::class)
    <div class="space-y-6">
        {{-- Header & Action Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">RFQs & Supplier Quotations</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Manage vendor price quotes, evaluate supplier offers, and trigger purchase orders.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\SupplierQuotation::class)
                    <a href="{{ route('admin.procurement.rfqs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md focus:ring-2 focus:ring-indigo-500 focus:outline-none shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Record New Quotation
                    </a>
                @endcan
            </div>
        </div>

        {{-- Overview Stats Bar --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Quotes</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-xs text-slate-400 mt-0.5">Recorded vendor quotes</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Quoted Value</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-xl font-extrabold text-slate-900 truncate">
                    {{ format_currency($stats['total_value'] ?? 0, session('currency', 'RWF')) }}
                </div>
                <div class="text-xs text-slate-400 mt-0.5">Combined value of quotes</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Approved Quotes</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-2xl font-extrabold text-emerald-600">{{ $stats['approved'] ?? 0 }}</div>
                <div class="text-xs text-slate-400 mt-0.5">Accepted & converted to PO</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Drafts</span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-2xl font-extrabold text-slate-700">{{ $stats['draft'] ?? 0 }}</div>
                <div class="text-xs text-slate-400 mt-0.5">Under evaluation</div>
            </div>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.procurement.rfqs.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search by quote number or supplier name..." value="{{ request('search') }}" class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors shadow-xs">
                </div>

                <div class="w-full sm:w-48">
                    <select name="status" onchange="this.form.submit()" class="block w-full px-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors shadow-xs">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors shadow-xs border border-slate-200">
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request()->has('search') || request()->has('status'))
                        <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-slate-500 hover:text-slate-900 bg-white border border-slate-300 rounded-xl transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Quote Code & Date</th>
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Requisition Ref</th>
                            <th class="py-3.5 px-6 text-center">Items</th>
                            <th class="py-3.5 px-6 text-right">Quote Total</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right w-36">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($rfqs as $rfq)
                            @php
                                $statusNormalized = strtolower($rfq->status ?? 'draft');
                                $statusClasses = [
                                    'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'submitted' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ][$statusNormalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                
                                $quoteTotal = $rfq->items ? $rfq->items->sum(function($item) {
                                    return $item->total > 0 ? $item->total : (($item->quantity * $item->unit_price) - $item->discount + $item->tax);
                                }) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Code & Valid Date --}}
                                <td class="py-4 px-6">
                                    <a href="{{ route('admin.procurement.rfqs.show', $rfq->id) }}" class="text-sm font-extrabold text-slate-900 hover:text-indigo-600 transition-colors">
                                        {{ $rfq->code }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Valid until {{ $rfq->valid_until ? $rfq->valid_until->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>

                                {{-- Supplier --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 border border-indigo-100">
                                            {{ strtoupper(substr($rfq->supplier?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs">{{ $rfq->supplier?->name ?? 'Unknown Supplier' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $rfq->supplier?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- PR Reference --}}
                                <td class="py-4 px-6">
                                    @if($rfq->purchaseRequisition)
                                        <a href="{{ route('admin.procurement.requisitions.show', $rfq->purchaseRequisition->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $rfq->purchaseRequisition->code }}
                                        </a>
                                    @else
                                        <span class="text-xs font-medium text-slate-400 italic">Direct Quote</span>
                                    @endif
                                </td>

                                {{-- Line Items Count --}}
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $rfq->items ? $rfq->items->count() : 0 }} Items
                                    </span>
                                </td>

                                {{-- Quote Total Amount --}}
                                <td class="py-4 px-6 text-right font-extrabold text-slate-900 text-sm">
                                    {{ format_currency($quoteTotal, session('currency', 'RWF')) }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                                        {{ $rfq->status }}
                                    </span>
                                </td>

                                {{-- Row Action Icons & Dropdown --}}
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View Details Icon Link --}}
                                        <a href="{{ route('admin.procurement.rfqs.show', $rfq->id) }}" 
                                           title="View Details" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        @if($rfq->purchaseRequisition)
                                            {{-- Compare Quotes Icon Link --}}
                                            <a href="{{ route('admin.procurement.requisitions.compare', $rfq->purchaseRequisition->id) }}" 
                                               title="Compare PR Quotes" 
                                               class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            </a>
                                        @endif

                                        {{-- Dropdown Action Menu --}}
                                        <x-action-dropdown>
                                            @can('view', $rfq)
                                                <x-action-dropdown-item href="{{ route('admin.procurement.rfqs.show', $rfq->id) }}" icon="view">
                                                    View Details
                                                </x-action-dropdown-item>
                                            @endcan

                                            @if($rfq->purchaseRequisition)
                                                <x-action-dropdown-item href="{{ route('admin.procurement.requisitions.compare', $rfq->purchaseRequisition->id) }}">
                                                    Compare PR Quotes
                                                </x-action-dropdown-item>
                                            @endif

                                            @can('update', $rfq)
                                                <x-action-dropdown-item href="{{ route('admin.procurement.rfqs.edit', $rfq->id) }}" icon="edit">
                                                    Edit Quotation
                                                </x-action-dropdown-item>
                                            @endcan

                                            @can('delete', $rfq)
                                                <form method="POST" action="{{ route('admin.procurement.rfqs.destroy', $rfq->id) }}" id="delete-rfq-form-{{ $rfq->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('delete-rfq-form-{{ $rfq->id }}').submit()" icon="delete" variant="danger">
                                                    Delete Quotation
                                                </x-action-dropdown-item>
                                            @endcan
                                        </x-action-dropdown>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-200/80">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">No supplier quotations found</h3>
                                    <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto mb-4">There are currently no recorded quotations in the system. Click below to record your first supplier quotation.</p>
                                    @can('create', App\Models\SupplierQuotation::class)
                                        <a href="{{ route('admin.procurement.rfqs.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-all">
                                            Record New Quotation
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($rfqs, 'hasPages') && $rfqs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $rfqs->links() }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-7 w-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Access Denied</h3>
        <p class="text-xs text-slate-500 font-medium">You do not have permission to view Request for Quotations.</p>
    </div>
    @endcan
</x-layouts.admin>