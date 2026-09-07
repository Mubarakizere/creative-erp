<x-layouts.admin title="Purchase Orders">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Purchase Orders'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\PurchaseOrder::class)
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Purchase Orders</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Issue, track, and manage official purchase orders (PO) to suppliers.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\PurchaseOrder::class)
                    <a href="{{ route('admin.procurement.pos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        New Purchase Order
                    </a>
                @endcan
            </div>
        </div>

        {{-- KPI Statistics Banner --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Purchase Orders</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? $pos->total() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">All issued POs</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Approved & Sent</div>
                    <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['approved'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Ready for delivery</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Goods Received</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['received'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">GRN generated</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total PO Commitment</div>
                    <div class="text-xl font-black text-indigo-700 mt-1">
                        {{ format_currency($stats['total_value'] ?? 0, session('currency')) }}
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">Currency: {{ session('currency', 'RWF') }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Status Filter Tabs --}}
            <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                @php
                    $currentStatus = request('status');
                @endphp
                <a href="{{ route('admin.procurement.pos.index', array_filter(['search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ !$currentStatus ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    All POs
                </a>
                <a href="{{ route('admin.procurement.pos.index', array_filter(['status' => 'draft', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $currentStatus === 'draft' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Draft
                </a>
                <a href="{{ route('admin.procurement.pos.index', array_filter(['status' => 'approved', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $currentStatus === 'approved' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Approved
                </a>
                <a href="{{ route('admin.procurement.pos.index', array_filter(['status' => 'partially_received', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $currentStatus === 'partially_received' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Partially Received
                </a>
                <a href="{{ route('admin.procurement.pos.index', array_filter(['status' => 'received', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $currentStatus === 'received' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Received
                </a>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.procurement.pos.index') }}" class="w-full md:w-72 flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search PO code, supplier..." value="{{ request('search') }}" class="block w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.procurement.pos.index', array_filter(['status' => request('status')])) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- Main Table Component --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Order Code</th>
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Order Date</th>
                            <th class="py-3.5 px-6 text-right">Line Items</th>
                            <th class="py-3.5 px-6 text-right">Total Amount</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($pos as $po)
                            @php
                                $statusNormalized = strtolower($po->status ?? 'draft');
                                $statusClasses = [
                                    'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'sent' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'partially_received' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'received' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ][$statusNormalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';

                                $poTotal = $po->grand_total > 0 ? $po->grand_total : $po->items->sum(fn($i) => $i->total > 0 ? $i->total : ($i->quantity * $i->unit_price));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-4 px-6 font-extrabold text-slate-900">
                                    <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="hover:text-indigo-600 transition-colors flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <span>{{ $po->code }}</span>
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $po->supplier?->name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $po->supplier?->code ?? '' }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-600">
                                    {{ $po->order_date ? $po->order_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-slate-700">
                                    {{ $po->items->count() }} items
                                </td>
                                <td class="py-4 px-6 text-right font-black text-slate-900">
                                    {{ format_currency($poTotal, session('currency')) }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClasses }}">
                                        {{ str_replace('_', ' ', $po->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <x-action-dropdown>
                                        @can('view', $po)
                                            <x-action-dropdown-item href="{{ route('admin.procurement.pos.show', $po->id) }}" icon="view">
                                                View Order
                                            </x-action-dropdown-item>
                                        @endcan

                                        @if(strtolower($po->status) === 'draft')
                                            @can('approve', $po)
                                                <form action="{{ route('admin.procurement.pos.approve', $po->id) }}" method="POST" id="approve-po-form-{{ $po->id }}">
                                                    @csrf
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('approve-po-form-{{ $po->id }}').submit()">
                                                    Approve PO
                                                </x-action-dropdown-item>
                                            @endcan
                                        @elseif(in_array(strtolower($po->status), ['approved', 'partially_received']))
                                            @can('create', App\Models\GoodsReceipt::class)
                                                <x-action-dropdown-item href="{{ route('admin.procurement.receipts.create', ['po_id' => $po->id]) }}">
                                                    Receive Goods (GRN)
                                                </x-action-dropdown-item>
                                            @endcan
                                        @endif

                                        @can('delete', $po)
                                            <form action="{{ route('admin.procurement.pos.destroy', $po->id) }}" method="POST" id="delete-po-form-{{ $po->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this Purchase Order?')) document.getElementById('delete-po-form-{{ $po->id }}').submit()" icon="delete" variant="danger">
                                                Delete PO
                                            </x-action-dropdown-item>
                                        @endcan
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 px-6 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">No purchase orders found</h3>
                                    <p class="text-slate-500 text-xs max-w-sm mx-auto mb-5">There are currently no purchase orders matching your search or filter criteria.</p>
                                    @can('create', App\Models\PurchaseOrder::class)
                                        <a href="{{ route('admin.procurement.pos.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                                            Create Purchase Order
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($pos, 'hasPages') && $pos->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $pos->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
        <p class="text-sm text-slate-500 font-medium">You do not have permission to view purchase orders.</p>
    </div>
    @endcan
</x-layouts.admin>