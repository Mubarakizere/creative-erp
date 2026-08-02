<x-layouts.admin title="Stock Movements">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Movements'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Movements</h1>
            <p class="mt-1 text-sm text-gray-500">Move stock between bins, zones, and warehouses.</p>
        </div>
        @can('create', App\Models\WarehouseMovement::class)
            <x-button type="primary" href="{{ route('admin.warehouse.movements.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Movement
            </x-button>
        @endcan
    </div>


    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Movement #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">From → To</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $item->movement_number }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @php
                                    $typeLabels = [
                                        'bin_to_bin' => 'Bin → Bin',
                                        'zone_to_zone' => 'Zone → Zone',
                                        'warehouse_to_warehouse' => 'WH → WH',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20">
                                    {{ $typeLabels[$item->type] ?? $item->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $item->product->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ number_format($item->quantity, 0) }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $item->sourceWarehouse->name ?? '?' }} → {{ $item->destinationWarehouse->name ?? '?' }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
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
                                <a href="{{ route('admin.warehouse.movements.show', $item) }}" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">No movements found.</td>
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