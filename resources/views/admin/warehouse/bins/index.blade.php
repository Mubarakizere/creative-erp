<x-layouts.admin title="Warehouse Bins">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Warehouse Bins'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Warehouse Bins</h1>
            <p class="mt-1 text-sm text-gray-500">Manage Warehouse Bins.</p>
        </div>
        @can('create', App\Models\WarehouseBin::class)
            <x-button type="primary" href="{{ route('admin.warehouse.bins.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create New
            </x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Zone</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Location (A-R-S)</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Capacity</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bins as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">{{ $item->code }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $item->zone->name ?? 'N/A' }} 
                                <span class="text-xs text-gray-400">({{ $item->zone->warehouse->name ?? 'N/A' }})</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-medium">{{ $item->aisle ?? '-' }}</span> / 
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-medium">{{ $item->rack ?? '-' }}</span> / 
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-medium">{{ $item->shelf ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ number_format($item->current_quantity, 0) }} / {{ number_format($item->capacity, 0) }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'inactive' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                        'full' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'maintenance' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                    ];
                                    $colorClass = $statusColors[$item->status] ?? $statusColors['inactive'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ring-1 {{ $colorClass }}">
                                    {{ ucfirst($item->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.warehouse.bins.show', $item) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.warehouse.bins.edit', $item) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.warehouse.bins.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists($bins, 'links'))
                {{ $bins->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>