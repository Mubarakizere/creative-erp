<x-layouts.admin title="Create Pickings">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Pickings', 'url' => route('admin.warehouse.pickings.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Pickings</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.pickings.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Example Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="button" href="{{ route('admin.warehouse.pickings.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Save</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>