<x-layouts.admin title="New Inventory Adjustment">
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
        <p class="mt-1 text-sm text-gray-500">Record stock variances, damages, initial inventory intake, or cycle count reconciliations.</p>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="rounded-lg bg-rose-50 p-4 mb-6 border border-rose-200">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-rose-800">There were {{ $errors->count() }} validation errors</h3>
                    <div class="mt-1 text-sm text-rose-700">
                        <ul role="list" class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST"
          x-data="adjustmentForm()" id="adjustment-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Side: Items --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Adjustment Items</h3>
                        <button type="button"
                                @click="addItem()"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="p-4 rounded-lg border border-gray-200 bg-gray-50/50 relative group">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                    {{-- Product --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Material <span class="text-rose-500">*</span>
                                        </label>
                                        <select x-model="item.product_id"
                                                :name="`items[${index}][product_id]`"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                required>
                                            <option value="">Select Material...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                            @endforeach
                                        </select>
                                        <div x-show="item.product_id && getProductInfo(item.product_id)"
                                             class="mt-1.5 text-xs text-gray-500">
                                            Current stock:
                                            <span class="font-semibold text-gray-800" x-text="getProductInfo(item.product_id)?.total_stock || '0'"></span>
                                            <span x-text="getProductInfo(item.product_id)?.unit || 'units'"></span>
                                        </div>
                                    </div>

                                    {{-- Type --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Adjustment Type <span class="text-rose-500">*</span>
                                        </label>
                                        <select x-model="item.type"
                                                :name="`items[${index}][type]`"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                required>
                                            <option value="increase">Stock In (Increase)</option>
                                            <option value="decrease">Stock Out (Decrease)</option>
                                        </select>
                                    </div>

                                    {{-- Quantity --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Quantity <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               min="0.01"
                                               x-model="item.quantity"
                                               :name="`items[${index}][quantity]`"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </div>

                                {{-- Remove Button --}}
                                <button type="button"
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="absolute top-3 right-3 p-1.5 text-gray-300 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors opacity-0 group-hover:opacity-100"
                                        title="Remove item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Add row button --}}
                        <button type="button"
                                @click="addItem()"
                                class="w-full py-3 border-2 border-dashed border-gray-200 rounded-lg text-sm text-gray-500 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50/30 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Another Item
                        </button>
                    </div>
                </x-card>
            </div>

            {{-- Right Side: Details --}}
            <div class="space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Adjustment Details</h3>

                    <div class="space-y-4">
                        {{-- Warehouse --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Target Warehouse <span class="text-rose-500">*</span>
                            </label>
                            <select name="warehouse_id"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reason <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="reason"
                                   x-model="reason"
                                   placeholder="e.g. Initial Stock Intake, Audit Discrepancy"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                   required>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <button type="button" @click="setReason('Initial Stock')"
                                        class="text-xs px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                    Initial Stock
                                </button>
                                <button type="button" @click="setReason('Damaged / Expired')"
                                        class="text-xs px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                    Damaged
                                </button>
                                <button type="button" @click="setReason('Audit Variance')"
                                        class="text-xs px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                    Audit Variance
                                </button>
                                <button type="button" @click="setReason('Cycle Count')"
                                        class="text-xs px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                                    Cycle Count
                                </button>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notes <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea name="comments"
                                      rows="3"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                      placeholder="Reference slip numbers, auditor notes...">{{ old('comments') }}</textarea>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Total Items</span>
                            <span class="font-semibold text-gray-900" x-text="getTotalItems()"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Stock In</span>
                            <span class="font-semibold text-emerald-600" x-text="getIncreaseCount()"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Stock Out</span>
                            <span class="font-semibold text-rose-600" x-text="getDecreaseCount()"></span>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-button type="ghost" href="{{ route('admin.inventory.adjustments.index') }}">Cancel</x-button>
            <x-button type="primary" submit>Submit Adjustment</x-button>
        </div>
    </form>

    @push('scripts')
    <script>
        function adjustmentForm() {
            return {
                items: [{ id: Date.now(), product_id: '', type: 'increase', quantity: 1 }],
                productMap: @json($productData),
                reason: '{{ old('reason') }}',

                addItem() {
                    this.items.push({ id: Date.now() + Math.random(), product_id: '', type: 'increase', quantity: 1 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                setReason(val) {
                    this.reason = val;
                },
                getProductInfo(id) {
                    return this.productMap[id] || null;
                },
                getTotalItems() {
                    return this.items.length;
                },
                getIncreaseCount() {
                    return this.items.filter(function(i) { return i.type === 'increase'; }).length;
                },
                getDecreaseCount() {
                    return this.items.filter(function(i) { return i.type === 'decrease'; }).length;
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
