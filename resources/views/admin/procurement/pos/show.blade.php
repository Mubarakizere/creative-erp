<x-layouts.admin title="Purchase Order">
    <div class="px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Purchase Order: {{ $po->code }}</h1>
            <div>
                @if($po->status === 'draft')
                <form action="{{ route('admin.procurement.pos.approve', $po->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Approve PO</button>
                </form>
                @elseif($po->status === 'approved')
                <a href="{{ route('admin.procurement.receipts.create', ['po_id' => $po->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded">Receive Goods</a>
                @endif
            </div>
        </div>
        <div class="mt-6">
            <p><strong>Supplier:</strong> {{ $po->supplier?->name }}</p>
            <p><strong>Status:</strong> {{ $po->status }}</p>
            <p><strong>Order Date:</strong> {{ $po->order_date }}</p>
        </div>
        <h3 class="text-lg font-bold mt-6">Items</h3>
        <table class="w-full mt-2">
            <thead>
                <tr>
                    <th class="text-left">Product</th>
                    <th class="text-left">Qty</th>
                    <th class="text-left">Unit Price</th>
                    <th class="text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ $item->unit_price }}</td>
                    <td>${{ $item->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>