<x-layouts.admin title="Products">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => '#'],
                ['label' => 'Products'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Products & Services</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your catalog of physical products, services, raw materials, and finished goods.</p>
            </div>
            @can('create', App\Models\Product::class)
                <x-button type="primary" href="{{ route('admin.inventory.products.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Product
                </x-button>
            @endcan
        </div>
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.inventory.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input
                name="search"
                placeholder="Search products by name, SKU or barcode..."
                :value="request('search')"
                :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'"
            />

            <x-select
                name="type"
                placeholder="All Types"
                :options="['physical' => 'Physical Product', 'service' => 'Service', 'raw_material' => 'Raw Material', 'finished_good' => 'Finished Good']"
                :selected="request('type')"
            />

            <div class="flex items-end gap-2 lg:col-span-2">
                <x-button type="primary" size="md">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'type']))
                    <x-button type="ghost" href="{{ route('admin.inventory.products.index') }}" size="md">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">Image</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($products as $product)
            <tr @class(['bg-red-50/30' => $product->trashed()])>
                <td class="px-4 py-3">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                </td>

                <td class="px-4 py-3">
                    <div>
                        <a href="{{ route('admin.inventory.products.edit', $product) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $product->name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">SKU: {{ $product->sku }} {{ $product->barcode ? '| Barcode: '.$product->barcode : '' }}</p>
                    </div>
                </td>

                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $product->type) }}</span>
                </td>

                <td class="px-4 py-3 text-right">
                    <div class="text-sm font-medium text-gray-900">${{ number_format($product->selling_price, 2) }}</div>
                    <div class="text-xs text-gray-500">Cost: ${{ number_format($product->cost_price, 2) }}</div>
                </td>

                <td class="px-4 py-3">
                    <div class="text-sm text-gray-600">{{ $product->category?->name ?? '-' }}</div>
                </td>

                <td class="px-4 py-3">
                    @php
                        $statusType = match($product->status) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ ucfirst($product->status) }}</x-badge>
                </td>

                <td class="px-4 py-3 text-right">
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1"
                             style="display: none;">

                            @can('update', $product)
                                <a href="{{ route('admin.inventory.products.edit', $product) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            @endcan

                            <div class="border-t border-gray-100 my-1"></div>

                            @can('delete', $product)
                                <form method="POST" action="{{ route('admin.inventory.products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">No products found</p>
                        @can('create', App\Models\Product::class)
                            <x-button type="primary" href="{{ route('admin.inventory.products.create') }}" class="mt-4" size="sm">
                                Create Product
                            </x-button>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $products->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
