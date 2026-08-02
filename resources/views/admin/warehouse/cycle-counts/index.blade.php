<x-layouts.admin title="Cycle Counts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Cycle Counts'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cycle Counting</h1>
            <p class="mt-1 text-sm text-gray-500">Manage manual and scheduled physical inventory counts.</p>
        </div>
        @can('create', App\Models\WarehouseCycleCount::class)
            <x-button type="primary" href="{{ route('admin.warehouse.cycle-counts.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Initiate Count
            </x-button>
        @endcan
    </div>


    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Count #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Initiated</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cycleCounts as $count)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $count->count_number }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $count->warehouse->name ?? '—' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20">
                                    {{ ucfirst($count->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'variance_detected' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'approved' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                    ];
                                    $color = $statusColors[$count->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset">
                                    {{ str_replace('_', ' ', ucfirst($count->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $count->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.warehouse.cycle-counts.show', $count) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">No cycle counts initiated.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists($cycleCounts, 'links'))
                {{ $cycleCounts->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>