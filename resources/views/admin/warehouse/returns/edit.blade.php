<x-layouts.admin title="Edit Returns">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Returns', 'url' => route('admin.warehouse.returns.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Returns</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.returns.update', $item) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Example Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="pending" {{ ($item->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ ($item->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="button" href="{{ route('admin.warehouse.returns.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Update</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>