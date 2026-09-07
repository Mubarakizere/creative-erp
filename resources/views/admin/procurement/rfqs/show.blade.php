<x-layouts.admin title="RFQ Details - {{ $rfq->code }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'RFQs & Supplier Quotations', 'url' => route('admin.procurement.rfqs.index')],
                ['label' => $rfq->code],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.rfqs.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        RFQs & Quotations
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $rfq->code }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Quotation {{ $rfq->code }}
                    </h1>
                    @php
                        $statusNormalized = strtolower($rfq->status ?? 'draft');
                        $statusClasses = [
                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'submitted' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ][$statusNormalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-xs capitalize {{ $statusClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                        {{ $rfq->status }}
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                @if($rfq->purchaseRequisition)
                    <a href="{{ route('admin.procurement.requisitions.compare', $rfq->purchaseRequisition->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Compare All PR Quotes
                    </a>
                @endif

                @can('update', $rfq)
                    <a href="{{ route('admin.procurement.rfqs.edit', $rfq->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Quotation
                    </a>
                @endcan

                <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Overview Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Supplier Details Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Supplier</div>
                        <div class="text-base font-extrabold text-slate-900">{{ $rfq->supplier?->name ?? 'Unknown Supplier' }}</div>
                    </div>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div><span class="font-medium text-slate-400">Supplier Code:</span> {{ $rfq->supplier?->code ?? 'N/A' }}</div>
                    <div><span class="font-medium text-slate-400">Email:</span> {{ $rfq->supplier?->email ?? 'N/A' }}</div>
                    <div><span class="font-medium text-slate-400">Phone:</span> {{ $rfq->supplier?->phone ?? 'N/A' }}</div>
                </div>
            </div>

            {{-- Requisition Reference Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Purchase Requisition</div>
                        @if($rfq->purchaseRequisition)
                            <a href="{{ route('admin.procurement.requisitions.show', $rfq->purchaseRequisition->id) }}" class="text-base font-extrabold text-indigo-600 hover:underline">
                                {{ $rfq->purchaseRequisition->code }}
                            </a>
                        @else
                            <div class="text-base font-bold text-slate-700">Direct RFQ</div>
                        @endif
                    </div>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div><span class="font-medium text-slate-400">Project / Scope:</span> {{ $rfq->purchaseRequisition?->project?->name ?? 'General' }}</div>
                    <div><span class="font-medium text-slate-400">Requested By:</span> {{ $rfq->purchaseRequisition?->requestedBy?->name ?? 'System' }}</div>
                </div>
            </div>

            {{-- Dates & Terms Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dates & Lead Time</div>
                        <div class="text-base font-extrabold text-slate-900">
                            {{ $rfq->issue_date ? $rfq->issue_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div><span class="font-medium text-slate-400">Valid Until:</span> {{ $rfq->valid_until ? $rfq->valid_until->format('M d, Y') : 'N/A' }}</div>
                    @if($rfq->lead_time_days)
                        <div><span class="font-medium text-slate-400">Lead Time:</span> {{ $rfq->lead_time_days }} Days</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quoted Items Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Quotation Line Items</h3>
                    <p class="text-xs text-slate-500">Breakdown of product pricing, quantities, discounts, and taxes</p>
                </div>
                @php
                    $grandTotal = $rfq->items->sum(function($item) {
                        return $item->total > 0 ? $item->total : (($item->quantity * $item->unit_price) - $item->discount + $item->tax);
                    });
                @endphp
                <div class="text-right">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Quotation Grand Total</div>
                    <div class="text-xl font-black text-emerald-600">{{ format_currency($grandTotal, session('currency')) }}</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Product / Item</th>
                            <th class="py-3.5 px-6 text-center">Quantity</th>
                            <th class="py-3.5 px-6 text-right">Unit Price</th>
                            <th class="py-3.5 px-6 text-right">Discount</th>
                            <th class="py-3.5 px-6 text-right">Tax</th>
                            <th class="py-3.5 px-6 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($rfq->items as $item)
                            @php
                                $itemTotal = $item->total > 0 
                                    ? $item->total 
                                    : (($item->quantity * $item->unit_price) - $item->discount + $item->tax);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    {{ $item->product?->name ?? 'Product' }}
                                    @if($item->product?->unit)
                                        <span class="text-slate-400 font-normal text-[11px]">({{ $item->product->unit->name }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-700">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-semibold text-slate-900">
                                    {{ format_currency($item->unit_price, session('currency')) }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-emerald-600 font-medium">
                                    {{ $item->discount > 0 ? '-' . format_currency($item->discount, session('currency')) : '—' }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-slate-600">
                                    {{ $item->tax > 0 ? '+' . format_currency($item->tax, session('currency')) : '—' }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-black text-slate-900">
                                    {{ format_currency($itemTotal, session('currency')) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    No items recorded in this quotation.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100/90 border-t-2 border-slate-300 font-extrabold text-slate-900">
                            <td class="py-4 px-6 text-sm" colspan="5">
                                TOTAL QUOTATION AMOUNT
                            </td>
                            <td class="py-4 px-6 text-right text-base font-black text-emerald-600">
                                {{ format_currency($grandTotal, session('currency')) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
