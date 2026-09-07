<x-layouts.admin title="Purchase Order - {{ $po->code }}">
    <div class="space-y-6">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.pos.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Purchase Orders
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $po->code }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Purchase Order {{ $po->code }}
                    </h1>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'sent' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'partially_received' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'received' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ][strtolower($po->status ?? 'draft')] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border capitalize {{ $statusClasses }}">
                        {{ str_replace('_', ' ', $po->status) }}
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                @if($po->status === 'draft')
                    <button type="button" 
                            @click="$dispatch('open-modal', 'approve-po-modal')"
                            class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve Purchase Order
                    </button>
                @elseif(in_array($po->status, ['approved', 'partially_received']))
                    <a href="{{ route('admin.procurement.receipts.create', ['po_id' => $po->id]) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Receive Goods (GRN)
                    </a>
                @endif
                <a href="{{ route('admin.procurement.pos.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Approval Confirmation Modal --}}
        @if($po->status === 'draft')
            <x-modal id="approve-po-modal" maxWidth="lg">
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Approve Purchase Order</h3>
                            <p class="text-xs text-slate-500 font-normal">Confirm approval for {{ $po->code }}</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-semibold uppercase tracking-wider text-slate-400">Supplier</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $po->supplier?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold uppercase tracking-wider text-slate-400">Grand Total</div>
                            <div class="text-sm font-black text-indigo-600 mt-0.5">
                                {{ format_currency($po->grand_total > 0 ? $po->grand_total : $po->items->sum(fn($i) => $i->total > 0 ? $i->total : ($i->quantity * $i->unit_price)), session('currency')) }}
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 text-xs text-blue-900 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-bold">Confirmation Notice:</span> Approving this Purchase Order will mark it as approved and allow warehouse goods receipt (GRN) creation when goods arrive.
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <button type="button" @click="$dispatch('close-modal', 'approve-po-modal')" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 rounded-xl transition-colors shadow-xs">
                        Cancel
                    </button>
                    <form action="{{ route('admin.procurement.pos.approve', $po->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="px-5 py-2 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Confirm Approval
                        </button>
                    </form>
                </x-slot:footer>
            </x-modal>
        @endif

        {{-- Metadata Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Supplier Name</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $po->supplier?->name ?? 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $po->supplier?->email ?? $po->supplier?->phone ?? 'No contact info' }}</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Order Date</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $po->order_date ? $po->order_date->format('M d, Y') : 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Delivery: {{ $po->delivery_date ? $po->delivery_date->format('M d, Y') : 'Not specified' }}</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Items</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ $po->items->count() }} Line Items</div>
                <div class="text-xs text-slate-500 mt-0.5">Ordered goods count</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Grand Total</div>
                <div class="text-xl font-black text-indigo-600 mt-1">
                    {{ format_currency($po->grand_total > 0 ? $po->grand_total : $po->items->sum(fn($i) => $i->total > 0 ? $i->total : ($i->quantity * $i->unit_price)), session('currency')) }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">Currency: {{ session('currency', 'RWF') }}</div>
            </div>
        </div>

        {{-- Ordered Items Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Purchase Order Line Items</h3>
                <span class="text-xs text-slate-500">Pre-populated from accepted quotation</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4 text-center">Quantity</th>
                            <th class="py-3 px-4 text-right">Unit Price</th>
                            <th class="py-3 px-4 text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach($po->items as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $item->product?->name ?? 'Item' }}
                                    @if($item->product?->code)
                                        <span class="text-slate-400 font-normal text-[11px]">({{ $item->product->code }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                    {{ number_format($item->quantity, 2) }} {{ $item->product?->unit?->code ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">
                                    {{ format_currency($item->unit_price, session('currency')) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">
                                    {{ format_currency($item->total > 0 ? $item->total : ($item->quantity * $item->unit_price), session('currency')) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-200 font-extrabold text-slate-900">
                            <td colspan="3" class="py-4 px-4 text-right">Grand Total:</td>
                            <td class="py-4 px-4 text-right text-base text-indigo-600">
                                {{ format_currency($po->grand_total > 0 ? $po->grand_total : $po->items->sum(fn($i) => $i->total > 0 ? $i->total : ($i->quantity * $i->unit_price)), session('currency')) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Goods Receipts Associated --}}
        @if($po->goodsReceipts && $po->goodsReceipts->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="text-base font-bold text-slate-900 mb-3">Goods Receipts (GRN)</h3>
                <div class="space-y-2">
                    @foreach($po->goodsReceipts as $grn)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
                            <div class="font-bold text-slate-800">{{ $grn->code }}</div>
                            <div class="text-slate-500">{{ $grn->received_date ? $grn->received_date->format('M d, Y') : '' }}</div>
                            <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 capitalize">
                                {{ $grn->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>