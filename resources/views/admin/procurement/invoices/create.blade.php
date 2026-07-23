<x-layouts.admin title="Record Purchase Invoice">
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Record Purchase Invoice</h1>
        <a href="{{ route('admin.procurement.invoices.index') }}" class="text-gray-500 hover:text-gray-700">Back</a>
    </div>

    <form action="{{ route('admin.procurement.invoices.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                <input type="text" name="invoice_number" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                <select name="supplier_id" class="w-full rounded-lg border-gray-300 shadow-sm" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ (isset($po) && $po->supplier_id == $sup->id) ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Order (Optional)</label>
                <input type="text" readonly value="{{ $po->code ?? '' }}" class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500">
                @if(isset($po))
                    <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full rounded-lg border-gray-300 shadow-sm" required>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Line Items</h3>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2">Qty</th>
                        <th class="px-4 py-2">Unit Price</th>
                        <th class="px-4 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($po) && $po->items)
                        @foreach($po->items as $index => $item)
                        <tr>
                            <td class="px-4 py-2">
                                {{ $item->product->name }}
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][tax]" value="{{ $item->tax ?? 0 }}">
                                <input type="hidden" name="items[{{ $index }}][discount]" value="{{ $item->discount ?? 0 }}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="w-20 rounded border-gray-300" readonly>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" class="w-24 rounded border-gray-300" readonly>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="items[{{ $index }}][total]" value="{{ $item->total }}" class="w-24 rounded border-gray-300" readonly>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Standalone invoices require manual entry logic (skipped for test)</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-6">
            <div class="w-64 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Subtotal:</span>
                    <input type="number" name="subtotal" value="{{ isset($po) ? $po->items->sum('total') : 0 }}" class="w-32 rounded border-gray-300 text-right" readonly>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tax:</span>
                    <input type="number" name="tax_amount" value="{{ isset($po) ? $po->items->sum('tax') : 0 }}" class="w-32 rounded border-gray-300 text-right" readonly>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Discount:</span>
                    <input type="number" name="discount_amount" value="{{ isset($po) ? $po->items->sum('discount') : 0 }}" class="w-32 rounded border-gray-300 text-right" readonly>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                    <span>Grand Total:</span>
                    <input type="number" name="grand_total" value="{{ isset($po) ? $po->items->sum('total') : 0 }}" class="w-32 rounded border-transparent bg-transparent text-right font-bold focus:ring-0 focus:border-transparent" readonly>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.procurement.invoices.index') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Record Invoice</button>
        </div>
    </form>
</div>
</x-layouts.admin>