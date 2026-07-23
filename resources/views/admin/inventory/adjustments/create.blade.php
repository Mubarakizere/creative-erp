<x-layouts.admin title="New Adjustment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Adjustments', 'url' => route('admin.inventory.adjustments.index')],
                ['label' => 'New Adjustment'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Inventory Adjustment</h1>
        <p class="mt-1 text-sm text-gray-500">Create a request to adjust stock levels (requires approval).</p>
    </div>

    <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST"
          x-data="{
              items: [{ id: 1, product_id: '', type: 'increase', quantity: 1 }],
              addNewItem() {
                  this.items.push({ id: Date.now(), product_id: '', type: 'increase', quantity: 1 });
              },
              removeItem(index) {
                  this.items.splice(index, 1);
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Items Section --}}
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Adjustment Items</h2>
                        <x-button type="secondary" size="sm" @click="addNewItem" type="button">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Item
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50/50">
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
                                        <select x-model="item.product_id" :name="'items['+index+'][product_id]'" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="">Select Product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                                        <select x-model="item.type" :name="'items['+index+'][type]'" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="increase">Stock In (Increase)</option>
                                            <option value="decrease">Stock Out (Decrease)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" min="0.01" x-model="item.quantity" :name="'items['+index+'][quantity]'" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                </div>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="mt-7 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                {{-- Details Section --}}
                <x-card>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Adjustment Details</h2>
                    <div class="space-y-4">
                        @php
                            $warehouseOptions = $warehouses->pluck('name', 'id')->toArray();
                        @endphp
                        
                        <x-select name="warehouse_id" label="Warehouse" placeholder="Select Warehouse" :options="$warehouseOptions" required />
                        
                        <x-input name="reason" label="Reason" placeholder="e.g. Initial Stock, Damages" required />
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comments (Optional)</label>
                            <textarea name="comments" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.inventory.adjustments.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</a>
                        <x-button type="primary" submit>Submit Request</x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.admin>
