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
                    <div class="text-sm font-medium text-gray-900">{{ number_format($product->selling_price, 0) }} RWF</div>
                    <div class="text-xs text-gray-500">Cost: {{ number_format($product->cost_price, 0) }} RWF</div>
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
                    <x-action-dropdown>
                        @can('update', $product)
                            <x-action-dropdown-item href="{{ route('admin.inventory.products.edit', $product) }}" icon="edit">
                                Edit Product
                            </x-action-dropdown-item>
                        @endcan

                        @can('delete', $product)
                            <form method="POST" action="{{ route('admin.inventory.products.destroy', $product) }}" id="delete-product-form-{{ $product->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                            <x-action-dropdown-item onclick="document.getElementById('delete-product-form-{{ $product->id }}').submit()" icon="delete" variant="danger">
                                Delete Product
                            </x-action-dropdown-item>
                        @endcan
                    </x-action-dropdown>
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
