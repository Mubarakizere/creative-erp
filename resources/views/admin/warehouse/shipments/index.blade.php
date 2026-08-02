<x-layouts.admin title="Shipments">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Shipments'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shipments</h1>
            <p class="mt-1 text-sm text-gray-500">Manage outbound shipments and track their delivery status.</p>
        </div>
        @can('create', App\Models\WarehouseShipment::class)
            <x-button type="primary" href="{{ route('admin.warehouse.shipments.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Shipment
            </x-button>
        @endcan
    </div>


    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Shipment #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Carrier</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Tracking #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $item->shipment_number }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $item->carrier ?? '—' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700 font-mono">{{ $item->tracking_number ?? '—' }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'prepared' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'shipped' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                                        'delivered' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ];
                                    $color = $statusColors[$item->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $item->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.warehouse.shipments.show', $item) }}" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">View</a>
                                    @if(in_array($item->status, ['pending', 'prepared']))
                                        <a href="{{ route('admin.warehouse.shipments.edit', $item) }}" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No shipments found. Create one to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists($items, 'links'))
                {{ $items->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>