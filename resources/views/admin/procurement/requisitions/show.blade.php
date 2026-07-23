<x-layouts.admin title="View Requisition">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Requisition: {{ $requisition->code }}</h1>
        <p>Status: {{ $requisition->status }}</p>
        <p>Requested By: {{ $requisition->requestedBy?->name }}</p>

        <h3 class="text-lg font-bold mt-6">Items</h3>
        <ul>
            @foreach($requisition->items as $item)
                <li>{{ $item->product?->name }} - Qty: {{ $item->quantity }}</li>
            @endforeach
        </ul>
            <div class="mt-4">
            <a href="{{ route('admin.procurement.requisitions.compare', $requisition->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded inline-block">Compare Quotations</a>
        </div>
    </div>
</x-layouts.admin>