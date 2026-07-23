<x-layouts.admin title="View Receipt">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Goods Receipt: {{ $receipt->code }}</h1>
        <div class="mt-4">
            <p><strong>Purchase Order:</strong> {{ $receipt->purchaseOrder?->code }}</p>
            <p><strong>Warehouse:</strong> {{ $receipt->warehouse?->name }}</p>
            <p><strong>Date:</strong> {{ $receipt->receipt_date }}</p>
            <p><strong>Delivery Note:</strong> {{ $receipt->delivery_note_number }}</p>
        </div>
        <h3 class="text-lg font-bold mt-6">Items Received</h3>
        <table class="w-full mt-2">
            <thead>
                <tr>
                    <th class="text-left">Product</th>
                    <th class="text-left">Accepted Qty</th>
                    <th class="text-left">Rejected Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipt->items as $item)
                <tr>
                    <td>{{ $item->purchaseOrderItem?->product?->name }}</td>
                    <td class="text-green-600">{{ $item->received_quantity }}</td>
                    <td class="text-red-600">{{ $item->rejected_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>