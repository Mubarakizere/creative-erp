<x-layouts.admin title="Stock Counts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Stock Counts'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Counts</h1>
            <p class="mt-1 text-sm text-gray-500">Manual and cycle stock counts with variance tracking.</p>
        </div>
        <x-button type="primary" href="{{ route('admin.inventory.stock-counts.create') }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Count
        </x-button>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Variance</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($counts as $count)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $count->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $count->warehouse->name }}</td>
                            <td class="py-3 px-4">
                                @if($count->type === 'cycle')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Cycle</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Manual</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $count->items_count }} products</td>
                            <td class="py-3 px-4">
                                @if($count->variance_detected)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Yes</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">None</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($count->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                @elseif($count->status === 'counted')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Counted</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.inventory.stock-counts.show', $count) }}" class="text-xs font-medium text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-500">No stock counts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $counts->links('components.pagination') }}
        </div>
    </x-card>
</x-layouts.admin>
