<x-layouts.admin title="Warehouses">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Warehouses'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Warehouses</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your storage locations and zones.</p>
        </div>
        @can('create', App\Models\Warehouse::class)
            <x-button type="primary" href="{{ route('admin.inventory.warehouses.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Warehouse
            </x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Manager</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Zones</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($warehouses as $warehouse)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $warehouse->name }}</span>
                                    @if($warehouse->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Default
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $warehouse->location ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($warehouse->manager)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($warehouse->manager->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $warehouse->manager->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $warehouse->zones->count() }} Zones
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $warehouse->status === 'active' ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-red-600/20' }}">
                                    {{ ucfirst($warehouse->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('update', $warehouse)
                                        <a href="{{ route('admin.inventory.warehouses.edit', $warehouse) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete', $warehouse)
                                        <form action="{{ route('admin.inventory.warehouses.destroy', $warehouse) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this warehouse?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-sm font-medium">No warehouses found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $warehouses->links('components.pagination') }}
        </div>
    </x-card>
</x-layouts.admin>
