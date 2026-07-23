<x-layouts.admin title="Purchase Orders">
    <div class="px-4 py-8">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold">Purchase Orders</h1>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Order Date</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pos as $po)
                <tr>
                    <td>{{ $po->code }}</td>
                    <td>{{ $po->supplier?->name }}</td>
                    <td>{{ $po->order_date }}</td>
                    <td>{{ $po->status }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.pos.show', $po->id) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>