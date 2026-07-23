<x-layouts.admin title="Edit Brand">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Brands', 'url' => route('admin.inventory.brands.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Brand</h1>
        <p class="mt-1 text-sm text-gray-500">Update product brand details.</p>
    </div>

    <form action="{{ route('admin.inventory.brands.update', $brand) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="max-w-3xl">
            <x-card>
                <div class="space-y-6">
                    <x-input name="name" label="Brand Name" :value="$brand->name" required />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $brand->description }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-button type="ghost" href="{{ route('admin.inventory.brands.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Update Brand</x-button>
                </div>
            </x-card>
        </div>
    </form>
</x-layouts.admin>
