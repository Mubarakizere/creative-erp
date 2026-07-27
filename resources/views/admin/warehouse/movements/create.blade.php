<x-layouts.admin title="Create Movement">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Movements', 'url' => route('admin.warehouse.movements.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Request Stock Movement</h1>
        <p class="mt-1 text-sm text-gray-500">Transfer inventory between bins, zones, or entire warehouses.</p>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.movements.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Movement Type & Product -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-gray-200">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Movement Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="bin_to_bin">Bin to Bin (Same Warehouse)</option>
                        <option value="zone_to_zone">Zone to Zone (Same Warehouse)</option>
                        <option value="warehouse_to_warehouse">Warehouse to Warehouse</option>
                    </select>
                </div>
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Product <span class="text-red-500">*</span></label>
                    <select name="product_id" id="product_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="quantity" id="quantity" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Source -->
                <div class="space-y-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-2">Source Location</h3>
                    <div>
                        <label for="source_warehouse_id" class="block text-xs font-medium text-gray-700">Warehouse <span class="text-red-500">*</span></label>
                        <select name="source_warehouse_id" id="source_warehouse_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Select...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="source_zone_id" class="block text-xs font-medium text-gray-700">Zone</label>
                        <select name="source_zone_id" id="source_zone_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">None</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="source_bin_id" class="block text-xs font-medium text-gray-700">Bin</label>
                        <select name="source_bin_id" id="source_bin_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">None</option>
                            @foreach($bins as $b)
                                <option value="{{ $b->id }}">{{ $b->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Destination -->
                <div class="space-y-4 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <h3 class="text-sm font-bold text-blue-900 border-b border-blue-100 pb-2">Destination Location</h3>
                    <div>
                        <label for="destination_warehouse_id" class="block text-xs font-medium text-gray-700">Warehouse <span class="text-red-500">*</span></label>
                        <select name="destination_warehouse_id" id="destination_warehouse_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Select...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="destination_zone_id" class="block text-xs font-medium text-gray-700">Zone</label>
                        <select name="destination_zone_id" id="destination_zone_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">None</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="destination_bin_id" class="block text-xs font-medium text-gray-700">Bin</label>
                        <select name="destination_bin_id" id="destination_bin_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">None</option>
                            @foreach($bins as $b)
                                <option value="{{ $b->id }}">{{ $b->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Movement</label>
                <textarea name="reason" id="reason" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="E.g. Consolidation, overflow..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-200">
                <x-button type="button" href="{{ route('admin.warehouse.movements.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Request Movement</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>