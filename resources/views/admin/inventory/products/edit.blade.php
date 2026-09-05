<x-layouts.admin title="Edit Material">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Materials', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Edit ' . $product->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Material: {{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Update construction material details, preferred supplier, and tracking rules.</p>
        </div>
    </div>

    <form action="{{ route('admin.inventory.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Details --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Material Details</h3>
                    <div class="space-y-4">
                        <x-input name="name" label="Material Name" :value="old('name', $product->name)" placeholder="e.g. Portland Cement Grade 42.5 / Steel Rebar 12mm" required />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-input name="sku" label="Internal Material Code / SKU" :value="old('sku', $product->sku)" required />
                            <x-input name="barcode" label="Barcode / Tag" :value="old('barcode', $product->barcode)" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-select name="type" label="Material Type" required :options="[
                                'raw_material' => 'Raw Material (Cement, Steel, Aggregates)',
                                'consumable' => 'Consumable (Nails, Fuel, Safety Gear)',
                                'equipment' => 'Equipment / Tool (Drills, Mixers)',
                                'service' => 'Subcontractor / Service'
                            ]" :selected="old('type', $product->type)" />
                            <x-select name="status" label="Status" required :options="[
                                'active' => 'Active',
                                'inactive' => 'Inactive'
                            ]" :selected="old('status', $product->status)" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specifications / Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </x-card>

                {{-- Supplier & Procurement Information --}}
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Supplier & Procurement</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Procurement Link
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Preferred / Primary Supplier</label>
                                @if(Route::has('admin.suppliers.create'))
                                    <a href="{{ route('admin.suppliers.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Add New</a>
                                @endif
                            </div>
                            <x-select name="default_supplier_id" :options="$suppliers->pluck('name', 'id')->toArray()" :selected="old('default_supplier_id', $product->default_supplier_id)" placeholder="-- Select Primary Supplier --" />
                        </div>
                        <x-input name="supplier_sku" label="Supplier Part / Catalog #" :value="old('supplier_sku', $product->supplier_sku)" placeholder="e.g. SUP-CEM-001" />
                    </div>
                </x-card>

                {{-- Inventory Settings --}}
                <x-card x-data="{ trackInventory: {{ old('track_inventory', $product->track_inventory) ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Stock & Reorder Controls</h3>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="track_inventory" class="sr-only" x-model="trackInventory" value="1">
                                <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner" :class="{ 'bg-blue-600': trackInventory }"></div>
                                <div class="dot absolute w-4 h-4 bg-white rounded-full shadow top-1 left-1 transition" :class="{ 'transform translate-x-4': trackInventory }"></div>
                            </div>
                            <div class="ml-3 text-sm font-medium text-gray-700">Track Stock Quantity</div>
                        </label>
                    </div>

                    <div x-show="trackInventory" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-input name="minimum_stock" label="Minimum Stock Level" type="number" step="0.01" :value="old('minimum_stock', $product->minimum_stock)" min="0" required />
                            <x-input name="reorder_level" label="Reorder Trigger Level" type="number" step="0.01" :value="old('reorder_level', $product->reorder_level)" min="0" required />
                            <x-input name="safety_stock" label="Buffer / Safety Stock" type="number" step="0.01" :value="old('safety_stock', $product->safety_stock)" min="0" required />
                        </div>

                        <div class="pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_negative_stock" value="1" {{ old('allow_negative_stock', $product->allow_negative_stock) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Allow Issue Without Physical Stock</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="serial_numbers" value="1" {{ old('serial_numbers', $product->serial_numbers) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Track Equipment Serials</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="batch_numbers" value="1" {{ old('batch_numbers', $product->batch_numbers) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Track Material Batch / Lot #</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="expiration_date" value="1" {{ old('expiration_date', $product->expiration_date) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Has Expiration / Shelf Life</span>
                            </label>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                {{-- Pricing & Valuation --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Costing & Valuation</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input name="cost_price" label="Last Purchase Unit Cost" type="number" step="0.01" :value="old('cost_price', $product->cost_price)" placeholder="Auto-updated from Purchase Orders" />
                            <p class="mt-1 text-xs text-gray-500">Automatically updated whenever new stock arrives via Goods Receipt.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Valuation Method</label>
                            <select name="valuation_method" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="FIFO" {{ old('valuation_method', $product->valuation_method) == 'FIFO' ? 'selected' : '' }}>FIFO (First-In, First-Out) - Recommended</option>
                                <option value="Weighted Average" {{ old('valuation_method', $product->valuation_method) == 'Weighted Average' ? 'selected' : '' }}>Weighted Average Cost (WAC)</option>
                                <option value="Standard Cost" {{ old('valuation_method', $product->valuation_method) == 'Standard Cost' ? 'selected' : '' }}>Standard Fixed Cost</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Used when issuing materials to projects to accurately calculate project material expense.</p>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Tax</label>
                                <a href="{{ route('admin.finance.settings') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="tax_id" :options="$taxes->mapWithKeys(function($tax) { return [$tax->id => $tax->name . ' (' . $tax->rate . ($tax->type === 'percentage' ? '%' : '') . ')']; })->toArray()" :selected="old('tax_id', $product->tax_id)" placeholder="No Tax / Exempt" />
                        </div>
                    </div>
                </x-card>

                {{-- Classification --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Classification</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <a href="{{ route('admin.inventory.categories.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="product_category_id" :options="$categories->pluck('name', 'id')->toArray()" :selected="old('product_category_id', $product->product_category_id)" placeholder="Select Category" />
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Brand / Manufacturer</label>
                                <a href="{{ route('admin.inventory.brands.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="brand_id" :options="$brands->pluck('name', 'id')->toArray()" :selected="old('brand_id', $product->brand_id)" placeholder="Select Brand" />
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Unit of Measure (UoM)</label>
                                <a href="{{ route('admin.inventory.units.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="unit_of_measure_id" :options="$uoms->pluck('name', 'id')->toArray()" :selected="old('unit_of_measure_id', $product->unit_of_measure_id)" placeholder="Select UoM" />
                        </div>
                    </div>
                </x-card>

                {{-- Image --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Material Image / Photo</h3>
                    @if($product->image)
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">Current Photo:</p>
                            <img src="{{ Storage::disk('public')->url($product->image) }}" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    @endif
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload new image</span>
                                    <input id="file-upload" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between" x-data="{ showDeleteModal: false }">
            <div>
                @can('delete', $product)
                    <button type="button" @click="showDeleteModal = true" class="px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Material
                    </button>

                    <template x-teleport="body">
                        <div x-show="showDeleteModal" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-50 overflow-y-auto" 
                             style="display: none;"
                             @keydown.escape.window="showDeleteModal = false">
                            
                            <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="showDeleteModal = false"></div>

                            <div class="flex min-h-full items-center justify-center p-4">
                                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                        <div class="text-lg font-bold text-gray-900">Delete Material</div>
                                        <button @click="showDeleteModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="px-6 py-4 text-center">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4 border border-rose-200">
                                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete "{{ $product->name }}"?</h3>
                                        <p class="text-xs text-gray-500">Are you sure you want to delete this material (SKU: {{ $product->sku }})? It will be moved to archived materials.</p>
                                    </div>

                                    <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 justify-end">
                                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-xs">
                                            Cancel
                                        </button>
                                        <form action="{{ route('admin.inventory.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
                                                Delete Material
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                @endcan
            </div>

            <div class="flex gap-3">
                <x-button type="ghost" href="{{ route('admin.inventory.products.index') }}">Cancel</x-button>
                <x-button type="primary" submit>Save Changes</x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
