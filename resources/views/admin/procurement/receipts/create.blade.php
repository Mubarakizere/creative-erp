<x-layouts.admin title="Receive Goods">
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
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Purchase Order
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Receive Goods against {{ $po->code }}</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Record incoming inventory and generate a goods receipt.</p>
        </div>
    </div>

    <div class="mt-6">
        <form action="{{ route('admin.procurement.receipts.store') }}" method="POST" id="receipt-form">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Receipt Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="receipt_date" class="block text-sm font-medium text-gray-700 mb-1">Receipt Date <span class="text-red-500">*</span></label>
                            <input type="date" name="receipt_date" id="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('receipt_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="delivery_note_number" class="block text-sm font-medium text-gray-700 mb-1">Delivery Note Number</label>
                            <input type="text" name="delivery_note_number" id="delivery_note_number" value="{{ old('delivery_note_number') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('delivery_note_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Warehouse to Receive Into <span class="text-red-500">*</span></label>
                            <select id="warehouse_id" name="warehouse_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white">
                                <option value="">Select a Warehouse</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Items to Receive</h3>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Verify quantities for each item being received.</p>
                </div>
                
                @error('items') <p class="mt-4 px-6 text-sm text-red-600">{{ $message }}</p> @enderror
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Product</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Ordered</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-28">Received</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Remain</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Receiving Now</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Rejected</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($po->items as $index => $item)
                                @php
                                    $remaining = max(0, $item->quantity - $item->received_quantity);
                                @endphp
                                @if($remaining > 0)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">{{ $item->product?->name }}</span>
                                        <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $item->received_quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-blue-600 font-bold">{{ $remaining }}</td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="items[{{ $index }}][received_quantity]" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('items.'.$index.'.received_quantity', $remaining) }}" max="{{ $remaining }}" min="0" step="any" required>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="items[{{ $index }}][rejected_quantity]" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-red-600" value="{{ old('items.'.$index.'.rejected_quantity', 0) }}" min="0" step="any" required>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @if($po->items->filter(fn($i) => ($i->quantity - $i->received_quantity) > 0)->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                        All items for this purchase order have been fully received.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-xl hover:bg-green-700 shadow-sm transition-all focus:ring-2 focus:ring-green-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Submit Receipt (Updates Stock)
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to receive goods.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.pos.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Orders</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>