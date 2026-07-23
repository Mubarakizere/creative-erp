<x-layouts.admin title="New Reservation">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Reservations', 'url' => route('admin.inventory.reservations.index')],
                ['label' => 'New Reservation'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Reservation</h1>
        <p class="mt-1 text-sm text-gray-500">Hold stock for a specific quotation, invoice, or project.</p>
    </div>

    <form action="{{ route('admin.inventory.reservations.store') }}" method="POST"
          x-data="{
              warehouses: {{ Js::from($warehouses) }},
              warehouseId: '',
              getZones() {
                  let wh = this.warehouses.find(w => w.id == this.warehouseId);
                  return wh ? wh.zones : [];
              },
              referenceType: '',
              quotations: {{ Js::from($quotations) }},
              invoices: {{ Js::from($invoices) }},
              projects: {{ Js::from($projects) }},
              getReferences() {
                  if (this.referenceType === 'Quotation') return this.quotations;
                  if (this.referenceType === 'Invoice') return this.invoices;
                  if (this.referenceType === 'Project') return this.projects;
                  return [];
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                
                <x-card>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Stock Details</h2>
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
                            <select name="product_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="quantity" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional)</label>
                                <input type="date" name="expires_at" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                    </div>
                </x-card>

                <x-card>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Location</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse <span class="text-red-500">*</span></label>
                            <select x-model="warehouseId" name="warehouse_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select Warehouse...</option>
                                <template x-for="wh in warehouses" :key="wh.id">
                                    <option :value="wh.id" x-text="wh.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div x-show="getZones().length > 0">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Zone (Optional)</label>
                            <select name="zone_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Zone...</option>
                                <template x-for="z in getZones()" :key="z.id">
                                    <option :value="z.id" x-text="z.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </x-card>

            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Reference</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link to <span class="text-red-500">*</span></label>
                            <select x-model="referenceType" name="reference_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select type...</option>
                                <option value="Quotation">Quotation</option>
                                <option value="Invoice">Invoice</option>
                                <option value="Project">Project</option>
                            </select>
                        </div>
                        
                        <div x-show="referenceType !== ''">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Record <span class="text-red-500">*</span></label>
                            <select name="reference_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" :required="referenceType !== ''">
                                <option value="">Select record...</option>
                                <template x-for="ref in getReferences()" :key="ref.id">
                                    <option :value="ref.id" x-text="ref.id.substring(0,8) + ' - ' + (ref.name || 'Record')"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.inventory.reservations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</a>
                        <x-button type="primary" submit>Hold Stock</x-button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.admin>
