<x-layouts.admin title="Stock Transfers">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Transfers'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Transfers</h1>
            <p class="mt-1 text-sm text-gray-500">Manage inventory transfers between warehouses and zones.</p>
        </div>
        @can('create', App\Models\InventoryTransfer::class)
            <x-button type="primary" href="{{ route('admin.inventory.transfers.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Transfer
            </x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">From</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">To</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Tracking #</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Items Count</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transfers as $transfer)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $transfer->fromWarehouse->name }}</div>
                                @if($transfer->fromZone)
                                    <div class="text-xs text-gray-500">Zone: {{ $transfer->fromZone->name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $transfer->toWarehouse->name }}</div>
                                @if($transfer->toZone)
                                    <div class="text-xs text-gray-500">Zone: {{ $transfer->toZone->name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $transfer->tracking_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ count($transfer->items ?? []) }}</td>
                            <td class="py-3 px-4">
                                @if($transfer->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Completed</span>
                                @elseif($transfer->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                @elseif($transfer->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-500">No transfer records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $transfers->links('components.pagination') }}
        </div>
    </x-card>
</x-layouts.admin>
