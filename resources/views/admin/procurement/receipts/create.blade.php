<x-layouts.admin title="Receive Goods">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Receive Goods against {{ $po->code }}</h1>
        <form action="{{ route('admin.procurement.receipts.store') }}" method="POST" class="mt-6">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label>Receipt Date</label>
                    <input type="date" name="receipt_date" class="w-full border p-2" required>
                </div>
                <div class="w-1/2">
                    <label>Delivery Note Number</label>
                    <input type="text" name="delivery_note_number" class="w-full border p-2">
                </div>
            </div>
            <div class="mb-4">
                <label>Warehouse to Receive Into</label>
                <select name="warehouse_id" class="w-full border p-2" required>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <h3 class="font-bold mt-4">Items to Receive</h3>
            <table class="w-full mt-2">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Ordered Qty</th>
                        <th class="text-left">Received (Good)</th>
                        <th class="text-left">Rejected (Damaged)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $index => $item)
                    <tr>
                        <td>
                            {{ $item->product?->name }}
                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td><input type="number" name="items[{{ $index }}][received_quantity]" class="border p-2 w-24" value="{{ $item->quantity }}" required></td>
                        <td><input type="number" name="items[{{ $index }}][rejected_quantity]" class="border p-2 w-24" value="0" required></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit Receipt (Updates Stock)</button>
            </div>
        </form>
    </div>
</x-layouts.admin>