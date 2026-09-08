<x-layouts.admin title="Goods Receipt - {{ $receipt->code }}">
    <div class="space-y-6">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.receipts.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Goods Receipts
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $receipt->code }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Goods Receipt {{ $receipt->code }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 capitalize">
                        {{ $receipt->status ?? 'Completed' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($receipt->purchaseOrder)
                    <a href="{{ route('admin.procurement.pos.show', $receipt->purchaseOrder->id) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                        View Purchase Order ({{ $receipt->purchaseOrder->code }})
                    </a>
                @endif
                <a href="{{ route('admin.procurement.receipts.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Back to Receipts List
                </a>
            </div>
        </div>

        {{-- Metadata Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Purchase Order</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $receipt->purchaseOrder?->code ?? 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Supplier: {{ $receipt->purchaseOrder?->supplier?->name ?? 'N/A' }}</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Warehouse Destination</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $receipt->warehouse?->name ?? 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Inventory location</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Receipt Date</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->format('M d, Y') : 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">DN: {{ $receipt->delivery_note_number ?? 'N/A' }}</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Items Received</div>
                <div class="text-base font-extrabold text-emerald-600 mt-1">{{ $receipt->items->count() }} Line Items</div>
                <div class="text-xs text-slate-500 mt-0.5">Posted to stock</div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Received Line Items Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Product / Material</th>
                            <th class="py-3.5 px-6 text-center">Accepted Quantity</th>
                            <th class="py-3.5 px-6 text-center">Rejected Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach($receipt->items as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $item->purchaseOrderItem?->product?->name ?? 'Product Item' }}
                                    @if($item->purchaseOrderItem?->product?->code)
                                        <span class="text-slate-400 font-normal text-[11px]">({{ $item->purchaseOrderItem->product->code }})</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-extrabold text-emerald-600">
                                    +{{ number_format($item->received_quantity, 2) }} {{ $item->purchaseOrderItem?->product?->unit?->code ?? '' }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold {{ $item->rejected_quantity > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    {{ number_format($item->rejected_quantity, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>