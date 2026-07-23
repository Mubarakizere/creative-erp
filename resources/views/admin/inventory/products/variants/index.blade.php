<x-layouts.admin title="Product Variants">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Products', 'url' => route('admin.inventory.products.index')],
                ['label' => $product->name, 'url' => route('admin.inventory.products.edit', $product)],
                ['label' => 'Variants'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Variants for {{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Manage combinations of sizes, colors, and other options.</p>
        </div>
        <x-button type="ghost" href="{{ route('admin.inventory.products.edit', $product) }}">
            Back to Product
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Create Variant Form --}}
        <div class="lg:col-span-1">
            <x-card x-data="{ optionsCount: 1 }">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Variant</h3>
                <form action="{{ route('admin.inventory.products.variants.store', $product) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <x-input name="sku" label="Variant SKU" required placeholder="{{ $product->sku }}-V1" />
                        <x-input name="barcode" label="Variant Barcode" />
                        
                        <div class="border-t border-gray-100 pt-4 pb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Options (e.g. Size, Color)</label>
                            
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="options[Color]" placeholder="Option Name (Color)" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <input type="text" name="options[Size]" placeholder="Option Value (Large)" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                                <p class="text-xs text-gray-500">Enter key-value pairs like Color: Red, Size: XL</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input name="cost_price" label="Cost Price" type="number" step="0.01" />
                            <x-input name="selling_price" label="Selling Price" type="number" step="0.01" />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-input name="price_adjustment" label="Price Adjust (+/-)" type="number" step="0.01" value="0" />
                            <x-input name="weight" label="Weight" type="number" step="0.01" />
                        </div>
                        
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" selected="active" />

                        <x-button type="primary" submit class="w-full justify-center mt-4">Create Variant</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Variants List --}}
        <div class="lg:col-span-2">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Existing Variants</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Options</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price/Adj.</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-3 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($variants as $variant)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-sm text-gray-900 font-medium">{{ $variant->sku }}</td>
                                    <td class="py-2 px-3 text-sm text-gray-500">
                                        @foreach($variant->options ?? [] as $key => $value)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                                {{ $key }}: {{ $value }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-3 text-right">
                                        @if($variant->selling_price)
                                            <div class="text-sm font-medium text-gray-900">${{ number_format($variant->selling_price, 2) }}</div>
                                        @elseif($variant->price_adjustment != 0)
                                            <div class="text-sm font-medium {{ $variant->price_adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $variant->price_adjustment > 0 ? '+' : '' }}${{ number_format($variant->price_adjustment, 2) }}
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">Same as Base</div>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <x-badge :type="$variant->status === 'active' ? 'success' : 'warning'">
                                            {{ ucfirst($variant->status) }}
                                        </x-badge>
                                    </td>
                                    <td class="py-2 px-3 text-right">
                                        <form action="{{ route('admin.inventory.products.variants.destroy', [$product, $variant]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm text-gray-500">No variants exist for this product.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
