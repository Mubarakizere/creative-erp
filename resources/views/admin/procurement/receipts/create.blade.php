<x-layouts.admin title="Receive Goods - {{ $po->code }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Goods Receipts', 'url' => route('admin.procurement.receipts.index')],
                ['label' => 'Receive against ' . $po->code],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\GoodsReceipt::class)
    <div class="space-y-6" x-data="{
        fillAll() {
            document.querySelectorAll('.recv-qty-input').forEach(input => {
                input.value = input.getAttribute('max');
            });
        },
        clearAll() {
            document.querySelectorAll('.recv-qty-input').forEach(input => {
                input.value = 0;
            });
        }
    }">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Purchase Order {{ $po->code }}
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">Receive Goods (GRN)</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Receive Goods against {{ $po->code }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $po->code }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Cancel & Back
                </a>
            </div>
        </div>

        {{-- Purchase Order Reference Banner --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                <div class="flex items-center gap-3.5 pr-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Supplier</div>
                        <div class="text-sm font-bold text-slate-900">{{ $po->supplier?->name ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $po->supplier?->email ?? 'No contact' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Order Date</div>
                        <div class="text-sm font-bold text-slate-900">{{ $po->order_date ? $po->order_date->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">PO Status: <span class="capitalize font-bold text-slate-700">{{ str_replace('_', ' ', $po->status) }}</span></div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total PO Items</div>
                        <div class="text-sm font-bold text-slate-900">{{ $po->items->count() }} Line Items</div>
                        <div class="text-xs text-slate-500 mt-0.5">Ordered Goods Count</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">PO Value</div>
                        <div class="text-sm font-black text-indigo-600">
                            {{ format_currency($po->grand_total > 0 ? $po->grand_total : $po->items->sum(fn($i) => $i->total > 0 ? $i->total : ($i->quantity * $i->unit_price)), session('currency')) }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Currency: {{ session('currency', 'RWF') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Goods Receipt Form --}}
        <form action="{{ route('admin.procurement.receipts.store') }}" method="POST" id="receipt-form">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">

            {{-- Card 1: Receipt Details --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
                <div class="bg-slate-50/50 border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Goods Receipt Header</h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Step 1 of 2</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Receipt Number (GRN) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $code ?? '') }}" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs font-bold transition-colors">
                            @error('code') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="receipt_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Receipt Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="receipt_date" id="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs font-bold transition-colors">
                            @error('receipt_date') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="delivery_note_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Delivery Note / Waybill #
                            </label>
                            <input type="text" name="delivery_note_number" id="delivery_note_number" value="{{ old('delivery_note_number') }}" placeholder="e.g. DN-9842" class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs transition-colors">
                            @error('delivery_note_number') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="warehouse_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Warehouse Destination <span class="text-rose-500">*</span>
                            </label>
                            <select id="warehouse_id" name="warehouse_id" required class="block w-full rounded-xl border-slate-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs transition-colors bg-white font-medium">
                                <option value="">Select Target Warehouse</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Items Receiving Table --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
                <div class="bg-slate-50/50 border-b border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Line Items Receiving Matrix</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Verify and enter quantities received into inventory</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="fillAll()" class="px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors shadow-xs">
                            Receive All Remaining
                        </button>
                        <button type="button" @click="clearAll()" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 rounded-lg border border-slate-300 transition-colors shadow-xs">
                            Clear Quantities
                        </button>
                    </div>
                </div>

                @error('items') <p class="mt-3 px-6 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                                <th class="py-3.5 px-6 min-w-[220px]">Product / Material</th>
                                <th class="py-3.5 px-4 text-center min-w-[90px]">Ordered</th>
                                <th class="py-3.5 px-4 text-center min-w-[90px]">Received</th>
                                <th class="py-3.5 px-4 text-center min-w-[90px]">Remaining</th>
                                <th class="py-3.5 px-4 text-right min-w-[130px]">Receiving Now <span class="text-rose-500">*</span></th>
                                <th class="py-3.5 px-4 text-right min-w-[120px]">Rejected</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @foreach($po->items as $index => $item)
                                @php
                                    $remaining = max(0, $item->quantity - $item->received_quantity);
                                @endphp
                                @if($remaining > 0)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-6 font-bold text-slate-900">
                                            <div>{{ $item->product?->name }}</div>
                                            @if($item->product?->code)
                                                <div class="text-[11px] font-normal text-slate-400 mt-0.5">Code: {{ $item->product->code }}</div>
                                            @endif
                                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                        </td>
                                        <td class="py-4 px-4 text-center font-semibold text-slate-600">
                                            {{ number_format($item->quantity, 2) }} {{ $item->product?->unit?->code ?? '' }}
                                        </td>
                                        <td class="py-4 px-4 text-center font-bold text-emerald-600">
                                            {{ number_format($item->received_quantity, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ number_format($remaining, 2) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <input type="number" 
                                                   name="items[{{ $index }}][received_quantity]" 
                                                   value="{{ old('items.'.$index.'.received_quantity', $remaining) }}" 
                                                   max="{{ $remaining }}" 
                                                   min="0" 
                                                   step="any" 
                                                   required 
                                                   class="recv-qty-input block w-full text-right rounded-xl border-slate-300 shadow-xs focus:border-emerald-500 focus:ring-emerald-500 text-xs font-extrabold text-emerald-700 bg-emerald-50/30">
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <input type="number" 
                                                   name="items[{{ $index }}][rejected_quantity]" 
                                                   value="{{ old('items.'.$index.'.rejected_quantity', 0) }}" 
                                                   min="0" 
                                                   step="any" 
                                                   required 
                                                   class="block w-full text-right rounded-xl border-slate-300 shadow-xs focus:border-rose-500 focus:ring-rose-500 text-xs font-bold text-rose-600 bg-rose-50/20">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if($po->items->filter(fn($i) => ($i->quantity - $i->received_quantity) > 0)->isEmpty())
                                <tr>
                                    <td colspan="6" class="py-12 px-6 text-center">
                                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">All Order Line Items Fully Received</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">All items in this purchase order have already been delivered and received into inventory.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Submit Controls --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between gap-4">
                <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 rounded-xl transition-colors shadow-xs">
                    Cancel
                </a>
                <button type="button" 
                        @click="$dispatch('open-modal', 'confirm-grn-modal')"
                        class="px-6 py-2.5 text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Submit Goods Receipt & Update Stock
                </button>
            </div>

            {{-- Global Confirmation Modal for GRN --}}
            <x-modal id="confirm-grn-modal" maxWidth="lg">
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Confirm Goods Receipt</h3>
                            <p class="text-xs text-slate-500 font-normal">Post incoming inventory to warehouse</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-semibold uppercase tracking-wider text-slate-400">Order Reference</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $po->code }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold uppercase tracking-wider text-slate-400">Supplier</div>
                            <div class="text-sm font-bold text-indigo-600 mt-0.5">{{ $po->supplier?->name ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-bold">Inventory Notice:</span> Submitting this Goods Receipt will immediately increment product stock levels in the selected warehouse and update PO receiving status.
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <button type="button" @click="$dispatch('close-modal', 'confirm-grn-modal')" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 rounded-xl transition-colors shadow-xs">
                        Review Details
                    </button>
                    <button type="submit" form="receipt-form" class="px-5 py-2 text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm & Update Stock
                    </button>
                </x-slot:footer>
            </x-modal>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
        <p class="text-sm text-slate-500 font-medium">You do not have permission to receive goods.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.pos.index') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-all">Return to Orders</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>