<x-layouts.admin title="Stock Levels & Adjustments">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Adjustments'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventory Adjustments</h1>
            <p class="mt-1 text-sm text-gray-500">View stock levels and request adjustments.</p>
        </div>
        @can('create', App\Models\InventoryAdjustment::class)
            <x-button type="primary" href="{{ route('admin.inventory.adjustments.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Adjustment
            </x-button>
        @endcan
    </div>

    <div x-data="{ tab: 'levels' }" class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'levels'"
                        :class="tab === 'levels' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Stock Levels
                </button>
                <button @click="tab = 'adjustments'"
                        :class="tab === 'adjustments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Adjustment Requests
                </button>
            </nav>
        </div>

        {{-- Stock Levels Tab --}}
        <div x-show="tab === 'levels'" class="mt-6" x-cloak>
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Available</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Reserved</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Incoming</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Outgoing</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($inventories as $inventory)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $inventory->product->name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $inventory->warehouse->name }}</td>
                                    <td class="py-3 px-4 text-right text-sm font-bold text-gray-900">{{ number_format($inventory->available_quantity, 2) }}</td>
                                    <td class="py-3 px-4 text-right text-sm text-gray-600">{{ number_format($inventory->reserved_quantity, 2) }}</td>
                                    <td class="py-3 px-4 text-right text-sm text-blue-600">{{ number_format($inventory->incoming_quantity, 2) }}</td>
                                    <td class="py-3 px-4 text-right text-sm text-red-600">{{ number_format($inventory->outgoing_quantity, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-sm text-gray-500">No inventory records found. Add an adjustment to stock in products.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Adjustments Tab --}}
        <div x-show="tab === 'adjustments'" class="mt-6" x-cloak>
            <x-card>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Items Count</th>
                                <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($adjustments as $adjustment)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $adjustment->warehouse->name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $adjustment->reason }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ count($adjustment->items ?? []) }}</td>
                                    <td class="py-3 px-4">
                                        @if($adjustment->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Completed</span>
                                        @elseif($adjustment->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                        @elseif($adjustment->status === 'rejected')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm text-gray-500">No adjustment requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $adjustments->links('components.pagination') }}
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
