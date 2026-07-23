<x-layouts.admin title="New Transfer">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Transfers', 'url' => route('admin.inventory.transfers.index')],
                ['label' => 'New Transfer'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Stock Transfer</h1>
        <p class="mt-1 text-sm text-gray-500">Create a request to transfer stock between warehouses or zones.</p>
    </div>

    <form action="{{ route('admin.inventory.transfers.store') }}" method="POST"
          x-data="{
              warehouses: {{ Js::from($warehouses) }},
              fromWarehouseId: '',
              toWarehouseId: '',
              items: [{ id: 1, product_id: '', quantity: 1 }],
              addNewItem() {
                  this.items.push({ id: Date.now(), product_id: '', quantity: 1 });
              },
              removeItem(index) {
                  this.items.splice(index, 1);
              },
              getFromZones() {
                  let wh = this.warehouses.find(w => w.id == this.fromWarehouseId);
                  return wh ? wh.zones : [];
              },
              getToZones() {
                  let wh = this.warehouses.find(w => w.id == this.toWarehouseId);
                  return wh ? wh.zones : [];
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Items Section --}}
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Transfer Items</h2>
                        <x-button type="secondary" size="sm" @click="addNewItem" type="button">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Item
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50/50">
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
                                        <select x-model="item.product_id" :name="'items['+index+'][product_id]'" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="">Select Product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
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
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Transfer Details</h2>
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Warehouse <span class="text-red-500">*</span></label>
                            <select x-model="fromWarehouseId" name="from_warehouse_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Origin...</option>
                                <template x-for="wh in warehouses" :key="wh.id">
                                    <option :value="wh.id" x-text="wh.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div x-show="getFromZones().length > 0">
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Zone (Optional)</label>
                            <select name="from_zone_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Zone...</option>
                                <template x-for="z in getFromZones()" :key="z.id">
                                    <option :value="z.id" x-text="z.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Warehouse <span class="text-red-500">*</span></label>
                            <select x-model="toWarehouseId" name="to_warehouse_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Destination...</option>
                                <template x-for="wh in warehouses" :key="wh.id">
                                    <option :value="wh.id" x-text="wh.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div x-show="getToZones().length > 0">
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Zone (Optional)</label>
                            <select name="to_zone_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Zone...</option>
                                <template x-for="z in getToZones()" :key="z.id">
                                    <option :value="z.id" x-text="z.name"></option>
                                </template>
                            </select>
                        </div>

                        <x-input name="tracking_number" label="Tracking Number (Optional)" placeholder="e.g. TRN-10029" />
                        
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.inventory.transfers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</a>
                        <x-button type="primary" submit>Submit Request</x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.admin>
