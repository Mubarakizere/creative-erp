<x-layouts.admin title="Warehouse Returns">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Returns'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Returns Management</h1>
            <p class="mt-1 text-sm text-gray-500">Log and inspect inbound returns from customers or suppliers.</p>
        </div>
        @can('create', App\Models\WarehouseReturn::class)
            <x-button type="primary" href="{{ route('admin.warehouse.returns.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Log New Return
            </x-button>
        @endcan
    </div>


    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Return #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Items Qty</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date Logged</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $return->return_number }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @php
                                    $typeLabels = [
                                        'customer_return' => 'Customer Return',
                                        'supplier_return' => 'Supplier Return',
                                        'damaged_stock' => 'Damaged Stock',
                                    ];
                                @endphp
                                {{ $typeLabels[$return->type] ?? ucfirst($return->type) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $return->warehouse->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @php
                                    $totalQty = collect($return->items)->sum('quantity');
                                @endphp
                                {{ number_format($totalQty, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'restocked' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'disposed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ];
                                    $color = $statusColors[$return->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $return->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.warehouse.returns.show', $return) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">Inspect</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">No returns logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists($returns, 'links'))
                {{ $returns->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>