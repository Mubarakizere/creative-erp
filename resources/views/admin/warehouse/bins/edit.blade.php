<x-layouts.admin title="Edit Warehouse Bins">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Warehouse Bins', 'url' => route('admin.warehouse.bins.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Warehouse Bins</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.bins.update', $item) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Warehouse Zone <span class="text-red-500">*</span></label>
                    <select name="warehouse_zone_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select a Zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('warehouse_zone_id', $bin->warehouse_zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }} ({{ $zone->warehouse->name ?? 'Unknown Warehouse' }})
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_zone_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Bin Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $bin->code ?? '') }}" required placeholder="e.g. CS-A1-RA-S1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="active" {{ old('status', $bin->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $bin->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="full" {{ old('status', $bin->status ?? '') == 'full' ? 'selected' : '' }}>Full</option>
                        <option value="maintenance" {{ old('status', $bin->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Aisle</label>
                    <input type="text" name="aisle" value="{{ old('aisle', $bin->aisle ?? '') }}" placeholder="e.g. A1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('aisle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rack</label>
                    <input type="text" name="rack" value="{{ old('rack', $bin->rack ?? '') }}" placeholder="e.g. Rack A" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('rack') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Shelf</label>
                    <input type="text" name="shelf" value="{{ old('shelf', $bin->shelf ?? '') }}" placeholder="e.g. Shelf 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('shelf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity (Weight/Volume)</label>
                    <input type="number" step="0.01" name="capacity" value="{{ old('capacity', $bin->capacity ?? 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('capacity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="button" href="{{ route('admin.warehouse.bins.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Update</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>