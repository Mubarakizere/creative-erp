<x-layouts.admin title="Edit Product">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Products', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Edit ' . $product->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Product: {{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Update product information and settings.</p>
        </div>
    </div>

    <form action="{{ route('admin.inventory.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Details --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <div class="space-y-4">
                        <x-input name="name" label="Product Name" :value="$product->name" required />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-input name="sku" label="SKU (Stock Keeping Unit)" :value="$product->sku" required />
                            <x-input name="barcode" label="Barcode" :value="$product->barcode" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-select name="type" label="Product Type" required :options="[
                                'physical' => 'Physical Product',
                                'service' => 'Service',
                                'raw_material' => 'Raw Material',
                                'finished_good' => 'Finished Good'
                            ]" :selected="$product->type" />
                            <x-select name="status" label="Status" required :options="[
                                'active' => 'Active',
                                'inactive' => 'Inactive'
                            ]" :selected="$product->status" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $product->description }}</textarea>
                        </div>
                    </div>
                </x-card>

                {{-- Inventory Settings --}}
                <x-card x-data="{ trackInventory: {{ $product->track_inventory ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Inventory Management</h3>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="track_inventory" class="sr-only" x-model="trackInventory" value="1">
                                <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner" :class="{ 'bg-blue-600': trackInventory }"></div>
                                <div class="dot absolute w-4 h-4 bg-white rounded-full shadow top-1 left-1 transition" :class="{ 'transform translate-x-4': trackInventory }"></div>
                            </div>
                            <div class="ml-3 text-sm font-medium text-gray-700">Track Inventory</div>
                        </label>
                    </div>

                    <div x-show="trackInventory" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-input name="minimum_stock" label="Minimum Stock" type="number" :value="$product->minimum_stock" min="0" required />
                            <x-input name="reorder_level" label="Reorder Level" type="number" :value="$product->reorder_level" min="0" required />
                            <x-input name="safety_stock" label="Safety Stock" type="number" :value="$product->safety_stock" min="0" required />
                            <x-input name="maximum_stock" label="Maximum Stock" type="number" :value="$product->maximum_stock" min="0" />
                        </div>

                        <div class="pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_negative_stock" value="1" {{ $product->allow_negative_stock ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Allow Negative Stock</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="serial_numbers" value="1" {{ $product->serial_numbers ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Track Serial Numbers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="batch_numbers" value="1" {{ $product->batch_numbers ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Track Batch Numbers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="expiration_date" value="1" {{ $product->expiration_date ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Has Expiration Date</span>
                            </label>
                        </div>
                    </div>
                </x-card>
                
                {{-- Product Variants UI --}}
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Product Variants</h3>
                        <x-button type="default" size="sm" href="{{ route('admin.inventory.products.variants.index', $product) }}">
                            Manage Variants
                        </x-button>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded relative" role="alert">
                      <span class="block sm:inline">You can manage variants (e.g. Size, Color) from the variants management tab.</span>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                {{-- Pricing --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h3>
                    <div class="space-y-4">
                        <x-input name="cost_price" label="Cost Price" type="number" step="0.01" :value="$product->cost_price" required />
                        <x-input name="selling_price" label="Selling Price" type="number" step="0.01" :value="$product->selling_price" required />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valuation Method</label>
                            <select name="valuation_method" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Standard Cost" {{ old('valuation_method', $product->valuation_method) == 'Standard Cost' ? 'selected' : '' }}>Standard Cost</option>
                                <option value="Weighted Average" {{ old('valuation_method', $product->valuation_method) == 'Weighted Average' ? 'selected' : '' }}>Weighted Average</option>
                                <option value="FIFO" {{ old('valuation_method', $product->valuation_method) == 'FIFO' ? 'selected' : '' }}>FIFO (First-In, First-Out)</option>
                            </select>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Tax</label>
                                <a href="{{ route('admin.finance.settings') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="tax_id" :options="$taxes->mapWithKeys(function($tax) { return [$tax->id => $tax->name . ' (' . $tax->rate . ($tax->type === 'percentage' ? '%' : '') . ')']; })->toArray()" :selected="$product->tax_id" placeholder="No Tax" />
                        </div>
                    </div>
                </x-card>

                {{-- Organization --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <a href="{{ route('admin.inventory.categories.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="product_category_id" :options="$categories->pluck('name', 'id')->toArray()" :selected="$product->product_category_id" placeholder="Select Category" />
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Brand</label>
                                <a href="{{ route('admin.inventory.brands.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="brand_id" :options="$brands->pluck('name', 'id')->toArray()" :selected="$product->brand_id" placeholder="Select Brand" />
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Unit of Measure</label>
                                <a href="{{ route('admin.inventory.units.create') }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Create New</a>
                            </div>
                            <x-select name="unit_of_measure_id" :options="$uoms->pluck('name', 'id')->toArray()" :selected="$product->unit_of_measure_id" placeholder="Select UoM" />
                        </div>
                    </div>
                </x-card>

                {{-- Product Image --}}
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Image</h3>
                    
                    @if($product->image)
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">Current Image:</p>
                        <img src="{{ Storage::disk('public')->url($product->image) }}" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                    @endif
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a new file</span>
                                    <input id="file-upload" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-button type="ghost" href="{{ route('admin.inventory.products.index') }}">Cancel</x-button>
            <x-button type="primary" submit>Save Changes</x-button>
        </div>
    </form>
</x-layouts.admin>
