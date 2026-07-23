<x-layouts.admin title="Inventory Dashboard">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventory Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Overview of your inventory health, valuation, and recent activities.</p>
        </div>
        <div class="flex gap-3">
            <x-button type="default" href="{{ route('admin.inventory.stock-counts.create') }}">Count Stock</x-button>
            <x-button type="primary" href="{{ route('admin.inventory.products.create') }}">Add Product</x-button>
        </div>
    </div>

    {{-- Top Metrics Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Inventory Value --}}
        <x-card class="flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Valuation</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900">${{ number_format($inventoryValue, 2) }}</h3>
                <p class="text-sm text-gray-500 mt-1">Total System Value</p>
            </div>
        </x-card>

        {{-- Product Count --}}
        <x-card class="flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Products</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($productCount) }}</h3>
                <p class="text-sm text-gray-500 mt-1">Tracked Products</p>
            </div>
        </x-card>

        {{-- Low Stock --}}
        <x-card class="flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Low Stock</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($lowStock->count()) }}</h3>
                <p class="text-sm text-gray-500 mt-1">Items below minimum</p>
            </div>
        </x-card>

        {{-- Out of Stock --}}
        <x-card class="flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase">Stockouts</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($outOfStock->count()) }}</h3>
                <p class="text-sm text-gray-500 mt-1">Items at zero stock</p>
            </div>
        </x-card>
    </div>

    {{-- Middle Section: Overstock & Warehouse Utilization --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Warehouse Utilization --}}
        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Warehouse Utilization</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Unique Products</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Total Items</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($warehouses as $warehouse)
                            <tr>
                                <td class="py-3 px-3 text-sm text-gray-900">{{ $warehouse['name'] }}</td>
                                <td class="py-3 px-3 text-sm text-gray-600 text-right">{{ number_format($warehouse['unique_products']) }}</td>
                                <td class="py-3 px-3 text-sm text-gray-900 font-medium text-right">{{ number_format($warehouse['total_items']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-sm text-gray-500">No active warehouse data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Overstock --}}
        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Overstock Alerts</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $overstock->count() }} Items
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Qty</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Max</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($overstock->take(5) as $item)
                            <tr>
                                <td class="py-3 px-3 text-sm text-gray-900">{{ $item['product']->name }}</td>
                                <td class="py-3 px-3 text-sm text-red-600 font-medium text-right">{{ number_format($item['qty']) }}</td>
                                <td class="py-3 px-3 text-sm text-gray-500 text-right">{{ number_format($item['product']->maximum_stock) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-sm text-gray-500">No overstocked items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Bottom Section: Recent Transactions & Pending Adjustments --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Recent Transactions --}}
        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="py-3 px-3 text-sm text-gray-500">{{ $tx->date->format('M d, H:i') }}</td>
                                <td class="py-3 px-3 text-sm text-gray-900 truncate max-w-[150px]">{{ $tx->inventory->product->name ?? 'Unknown' }}</td>
                                <td class="py-3 px-3 text-sm">
                                    @php
                                        $isPositive = $tx->quantity > 0;
                                        $typeLabel = str_replace('_', ' ', $tx->type);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isPositive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucwords($typeLabel) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-sm font-medium text-right {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isPositive ? '+' : '' }}{{ number_format($tx->quantity) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-sm text-gray-500">No recent transactions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Pending Adjustments --}}
        <x-card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Adjustments</h3>
                <a href="{{ route('admin.inventory.adjustments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Cost</th>
                            <th class="py-2 px-3 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pendingAdjustments as $adj)
                            <tr>
                                <td class="py-3 px-3 text-sm text-gray-500">{{ $adj->date->format('M d, Y') }}</td>
                                <td class="py-3 px-3 text-sm text-gray-900">{{ $adj->warehouse->name ?? '—' }}</td>
                                <td class="py-3 px-3 text-sm text-gray-900 font-medium text-right">${{ number_format($adj->total_cost, 2) }}</td>
                                <td class="py-3 px-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-sm text-gray-500">No pending adjustments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
