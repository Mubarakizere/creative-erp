<x-layouts.admin title="New Stock Count">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Stock Counts', 'url' => route('admin.inventory.stock-counts.index')],
                ['label' => 'New Count'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Stock Count</h1>
        <p class="mt-1 text-sm text-gray-500">Select a warehouse and the products you want to count.</p>
    </div>

    <form action="{{ route('admin.inventory.stock-counts.store') }}" method="POST"
          x-data="{
              selectAll: false,
              selectedProducts: [],
              toggleAll() {
                  if (this.selectAll) {
                      this.selectedProducts = {{ Js::from($products->pluck('id')) }};
                  } else {
                      this.selectedProducts = [];
                  }
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Products to Count</h2>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Select All
                        </label>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($products as $product)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" x-model="selectedProducts" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->sku ?? 'No SKU' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Count Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Count Type <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="manual">Manual (Full Count)</option>
                                <option value="cycle">Cycle Count (Partial)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-500" x-text="selectedProducts.length + ' products selected'"></span>
                        <x-button type="primary" submit>Start Count</x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.admin>
