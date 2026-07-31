<x-layouts.admin title="Edit Goods Receipt">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Goods Receipts', 'url' => route('admin.procurement.receipts.index')],
                ['label' => $receipt->code ?? 'Edit Receipt'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('update', $receipt)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.procurement.receipts.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Receipts
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Goods Receipt: {{ $receipt->code }}</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Modify existing receipt details. Note: Reversing stock requires specialized actions.</p>
        </div>
    </div>

    <div class="mt-6">
        <form action="{{ route('admin.procurement.receipts.update', $receipt->id) }}" method="POST" id="receipt-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Receipt Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="receipt_date" class="block text-sm font-medium text-gray-700 mb-1">Receipt Date <span class="text-red-500">*</span></label>
                            <input type="date" name="receipt_date" id="receipt_date" value="{{ old('receipt_date', $receipt->receipt_date) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('receipt_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="delivery_note_number" class="block text-sm font-medium text-gray-700 mb-1">Delivery Note Number</label>
                            <input type="text" name="delivery_note_number" id="delivery_note_number" value="{{ old('delivery_note_number', $receipt->delivery_note_number) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            @error('delivery_note_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-1">Warehouse Received Into <span class="text-red-500">*</span></label>
                            <select id="warehouse_id" name="warehouse_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white" disabled>
                                <option value="{{ $receipt->warehouse_id }}">{{ $receipt->warehouse?->name ?? 'Select Warehouse' }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Warehouse cannot be changed once stock is updated.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Received Items</h3>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Items that were received in this document.</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/60">
                        <thead class="bg-gray-50/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Product</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Received</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-32">Rejected</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($receipt->items ?? [] as $index => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">{{ $item->purchaseOrderItem?->product?->name ?? 'Unknown Product' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" readonly class="block w-full rounded-xl border-gray-200 shadow-sm sm:text-sm bg-gray-50 text-gray-500 cursor-not-allowed" value="{{ $item->quantity_received }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" readonly class="block w-full rounded-xl border-gray-200 shadow-sm sm:text-sm bg-gray-50 text-gray-500 cursor-not-allowed" value="{{ $item->quantity_rejected ?? 0 }}">
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($receipt->items ?? []) === 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 font-medium">
                                        No items recorded on this receipt.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="px-6 py-3 bg-yellow-50/50 border-t border-yellow-100 flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-xs text-yellow-800">Item quantities cannot be edited directly because stock levels have already been updated. If corrections are needed, use an inventory adjustment.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden px-6 py-4 flex items-center justify-end gap-3 mb-8">
                <a href="{{ route('admin.procurement.receipts.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Receipt Information
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
        <p class="text-sm text-gray-500 font-medium">You do not have permission to edit goods receipts.</p>
        <div class="mt-6">
            <a href="{{ route('admin.procurement.receipts.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Receipts</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
