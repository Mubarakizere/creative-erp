<x-layouts.admin title="Compare Quotations">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Compare Quotations: {{ $requisition->code }}</h1>
        <div class="mt-6 grid grid-cols-3 gap-6">
            @foreach($requisition->quotations as $quotation)
            <div class="border p-4 rounded shadow-sm">
                <h3 class="font-bold text-lg">{{ $quotation->supplier?->name }}</h3>
                <p>Status: {{ $quotation->status }}</p>
                <div class="mt-4">
                    @foreach($quotation->items as $item)
                        <div class="mb-2">
                            <span class="font-semibold">{{ $item->product?->name }}</span><br>
                            Qty: {{ $item->quantity }} | Price: ${{ $item->unit_price }}<br>
                            Discount: ${{ $item->discount }} | Tax: ${{ $item->tax }}<br>
                            <strong>Total: ${{ $item->total }}</strong>
                        </div>
                    @endforeach
                </div>
                @if($quotation->status === 'draft')
                <form action="{{ route('admin.procurement.requisitions.accept', [$requisition->id, $quotation->id]) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded text-center block">Accept & Generate PO</button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>