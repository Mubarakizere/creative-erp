<x-layouts.admin title="Edit Unit of Measure">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Units of Measure', 'url' => route('admin.inventory.units.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Unit of Measure</h1>
        <p class="mt-1 text-sm text-gray-500">Update unit of measure details.</p>
    </div>

    <form action="{{ route('admin.inventory.units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="max-w-3xl">
            <x-card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input name="name" label="Unit Name" :value="$unit->name" required />
                        <x-input name="abbreviation" label="Symbol/Abbreviation" :value="$unit->abbreviation" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $unit->description }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-button type="ghost" href="{{ route('admin.inventory.units.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Update Unit of Measure</x-button>
                </div>
            </x-card>
        </div>
    </form>
</x-layouts.admin>
