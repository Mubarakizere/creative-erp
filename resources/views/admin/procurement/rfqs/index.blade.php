<x-layouts.admin title="RFQs">
    <div class="px-4 py-8">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold">Request for Quotations</h1>
            <a href="{{ route('admin.procurement.rfqs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New RFQ</a>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Number</th>
                    <th class="text-left">Supplier</th>
                    <th class="text-left">Issue Date</th>
                    <th class="text-left">Valid Until</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rfqs as $rfq)
                <tr>
                    <td>{{ $rfq->code }}</td>
                    <td>{{ $rfq->supplier?->name }}</td>
                    <td>{{ $rfq->issue_date }}</td>
                    <td>{{ $rfq->valid_until }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>