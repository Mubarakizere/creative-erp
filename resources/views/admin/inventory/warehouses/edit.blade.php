<x-layouts.admin title="Edit Warehouse">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Warehouses', 'url' => route('admin.inventory.warehouses.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Warehouse</h1>
            <p class="mt-1 text-sm text-gray-500">Update warehouse details and manage zones.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.inventory.warehouses.update', $warehouse) }}" method="POST">
                @csrf
                @method('PUT')

                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Warehouse Details</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-input name="name" label="Warehouse Name" :value="$warehouse->name" required />
                            
                        @php
                            $managerOptions = $managers->pluck('full_name', 'id')->toArray();
                        @endphp
                        
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="$warehouse->status" required />
                        
                        <div class="sm:col-span-2">
                            <x-input name="location" label="Location / Address" :value="$warehouse->location" />
                        </div>
                        
                        <x-select name="manager_id" label="Manager" placeholder="Select a Manager (Optional)" :options="$managerOptions" :selected="$warehouse->manager_id" />
                            
                            <x-input name="capacity" type="number" step="0.01" label="Capacity (Optional)" :value="$warehouse->capacity" />
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-start gap-3">
                            <div class="flex items-center h-5">
                                <input id="is_default" name="is_default" type="checkbox" value="1" {{ $warehouse->is_default ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="text-sm">
                                <label for="is_default" class="font-medium text-gray-900">Set as Default Warehouse</label>
                                <p class="text-gray-500">Inventory will be assigned to this warehouse by default if not specified.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <x-button type="ghost" href="{{ route('admin.inventory.warehouses.index') }}">Cancel</x-button>
                        <x-button type="primary" submit>Update Warehouse</x-button>
                    </div>
                </x-card>
            </form>

            {{-- Warehouse Zones List --}}
            <div class="mt-8">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Warehouse Zones</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Zone Name</th>
                                    <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                    <th class="py-2 px-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="py-2 px-3 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouse->zones as $zone)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-2 px-3 text-sm font-medium text-gray-900">{{ $zone->name }}</td>
                                        <td class="py-2 px-3 text-sm text-gray-500">{{ Str::limit($zone->description, 50) }}</td>
                                        <td class="py-2 px-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $zone->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($zone->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 text-right">
                                            <form action="{{ route('admin.inventory.warehouses.zones.destroy', [$warehouse, $zone]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this zone?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-sm text-gray-500">No zones defined yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>

        {{-- Add Zone Sidebar --}}
        <div class="lg:col-span-1">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Zone</h3>
                <form action="{{ route('admin.inventory.warehouses.zones.store', $warehouse) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <x-input name="name" label="Zone Name" placeholder="e.g. Receiving Area" required />
                        
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" required />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <x-button type="primary" submit class="w-full justify-center">Add Zone</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
