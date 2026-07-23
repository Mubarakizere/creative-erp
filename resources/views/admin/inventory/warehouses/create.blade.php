<x-layouts.admin title="Create Warehouse">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Warehouses', 'url' => route('admin.inventory.warehouses.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Warehouse</h1>
        <p class="mt-1 text-sm text-gray-500">Add a new warehouse to manage your inventory.</p>
    </div>

    <form action="{{ route('admin.inventory.warehouses.store') }}" method="POST">
        @csrf

        <div class="max-w-3xl">
            <x-card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input name="name" label="Warehouse Name" placeholder="e.g. Main Distribution Center" required />
                        
                        @php
                            $managerOptions = $managers->pluck('full_name', 'id')->toArray();
                        @endphp
                        
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" required />
                        
                        <div class="sm:col-span-2">
                            <x-input name="location" label="Location / Address" placeholder="e.g. 123 Storage Rd, Warehouse City" />
                        </div>
                        
                        <x-select name="manager_id" label="Manager" placeholder="Select a Manager (Optional)" :options="$managerOptions" />

                        
                        <x-input name="capacity" type="number" step="0.01" label="Capacity (Optional)" placeholder="e.g. 5000" />
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input id="is_default" name="is_default" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="text-sm">
                            <label for="is_default" class="font-medium text-gray-900">Set as Default Warehouse</label>
                            <p class="text-gray-500">Inventory will be assigned to this warehouse by default if not specified. Only one warehouse can be default.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-button type="ghost" href="{{ route('admin.inventory.warehouses.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Create Warehouse</x-button>
                </div>
            </x-card>
        </div>
    </form>
</x-layouts.admin>
