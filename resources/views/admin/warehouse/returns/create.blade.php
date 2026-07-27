<x-layouts.admin title="Log New Return">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Returns', 'url' => route('admin.warehouse.returns.index')],
                ['label' => 'Log Return'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Log Inbound Return</h1>
        <p class="mt-1 text-sm text-gray-500">Record items coming back into the warehouse to prepare for inspection.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.warehouse.returns.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Details -->
            <div class="lg:col-span-1 space-y-6">
                <x-card>
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-100 pb-2">Return Details</h3>
                        
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Receiving Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Return Type <span class="text-red-500">*</span></label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="customer_return">Customer Return</option>
                                <option value="supplier_return">Supplier Return</option>
                                <option value="damaged_stock">Damaged Stock (Internal)</option>
                            </select>
                        </div>

                        <div class="pt-4 flex items-center">
                            <input id="requires_accounting_adjustment" name="requires_accounting_adjustment" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="requires_accounting_adjustment" class="ml-2 block text-sm text-gray-900">
                                Requires Accounting Adjustment?
                                <span class="block text-xs text-gray-500 font-normal">Check this if finance needs to write off losses from this return.</span>
                            </label>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Items -->
            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-6">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-2 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Items to Return</h3>
                            <button type="button" id="add-item-btn" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                + Add Product
                            </button>
                        </div>

                        <div id="items-container" class="space-y-4">
                            <!-- Template Row -->
                            <div class="item-row flex gap-4 items-end bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-700">Product</label>
                                    <select name="items[0][product_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">Select Product...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs font-medium text-gray-700">Quantity</label>
                                    <input type="number" step="0.01" name="items[0][quantity]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00">
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs font-medium text-gray-700">Unit Value (Optional)</label>
                                    <input type="number" step="0.01" name="items[0][unit_cost]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0.00">
                                </div>
                                <div>
                                    <button type="button" class="remove-btn mb-1 text-red-500 hover:text-red-700 p-2" title="Remove" disabled>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <x-button type="button" href="{{ route('admin.warehouse.returns.index') }}">Cancel</x-button>
                            <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Log Return for Inspection</x-button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('items-container');
            const addBtn = document.getElementById('add-item-btn');
            let index = 1;

            addBtn.addEventListener('click', () => {
                const row = container.querySelector('.item-row').cloneNode(true);
                
                // Update names
                row.querySelectorAll('select, input').forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                    input.value = '';
                });

                // Enable remove button
                const removeBtn = row.querySelector('.remove-btn');
                removeBtn.disabled = false;
                removeBtn.addEventListener('click', () => row.remove());

                container.appendChild(row);
                index++;
            });
        });
    </script>
</x-layouts.admin>