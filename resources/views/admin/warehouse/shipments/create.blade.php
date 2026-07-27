<x-layouts.admin title="Create Shipment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Shipments', 'url' => route('admin.warehouse.shipments.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Shipment</h1>
        <p class="mt-1 text-sm text-gray-500">Build a new shipment from completed packings.</p>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.shipments.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Warehouse -->
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouse_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select a warehouse</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                        @endforeach
                    </select>
                    @error('warehouse_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Carrier -->
                <div>
                    <label for="carrier" class="block text-sm font-medium text-gray-700">Carrier</label>
                    <input type="text" name="carrier" id="carrier" placeholder="e.g. FedEx, DHL, UPS" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('carrier') }}">
                </div>

                <!-- Tracking Number -->
                <div>
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700">Tracking Number</label>
                    <input type="text" name="tracking_number" id="tracking_number" placeholder="e.g. 1Z999AA10123456784" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono" value="{{ old('tracking_number') }}">
                </div>
            </div>

            <!-- Packings -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Attach Completed Packings</label>
                @if($packings->isEmpty())
                    <p class="text-sm text-gray-400 italic">No completed packings available to attach.</p>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($packings as $packing)
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                <input type="checkbox" name="packing_ids[]" value="{{ $packing->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-900">{{ $packing->packing_number }}</span>
                                <span class="text-xs text-gray-500">{{ $packing->total_weight }}kg</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Notes -->
            <div>
                <label for="shipping_notes" class="block text-sm font-medium text-gray-700">Shipping Notes</label>
                <textarea name="shipping_notes" id="shipping_notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Any special handling or delivery instructions...">{{ old('shipping_notes') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-200">
                <x-button type="button" href="{{ route('admin.warehouse.shipments.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Create Shipment</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>