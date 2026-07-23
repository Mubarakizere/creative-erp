<x-layouts.admin title="Goods Receipts">
    <div class="px-4 py-8">
        <h1 class="text-2xl font-bold">Goods Receipts</h1>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">PO</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $gr)
                <tr>
                    <td>{{ $gr->code }}</td>
                    <td>{{ $gr->purchaseOrder?->code }}</td>
                    <td>{{ $gr->purchaseOrder?->supplier?->name }}</td>
                    <td>{{ $gr->receipt_date }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.receipts.show', $gr->id) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>