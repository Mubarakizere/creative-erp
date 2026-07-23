<x-layouts.admin title="Purchase Requisitions">
    <div class="px-4 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Purchase Requisitions</h1>
            <a href="{{ route('admin.procurement.requisitions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Requisition</a>
        </div>
        <table class="w-full mt-6">
            <thead>
                <tr>
                    <th class="text-left">Code</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Requested By</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisitions as $pr)
                <tr>
                    <td>{{ $pr->code }}</td>
                    <td>{{ $pr->status }}</td>
                    <td>{{ $pr->requestedBy?->name }}</td>
                    <td>
                        <a href="{{ route('admin.procurement.requisitions.show', $pr->id) }}" class="text-blue-600">View</a>
                        @if($pr->status === 'submitted')
                        <form action="{{ route('admin.procurement.requisitions.approve', $pr->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-600 ml-2">Approve</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>