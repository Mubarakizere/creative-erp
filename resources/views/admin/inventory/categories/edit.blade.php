<x-layouts.admin title="Edit Category">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Categories', 'url' => route('admin.inventory.categories.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
        <p class="mt-1 text-sm text-gray-500">Update product category details.</p>
    </div>

    <form action="{{ route('admin.inventory.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="max-w-3xl">
            <x-card>
                <div class="space-y-6">
                    <x-input name="name" label="Category Name" :value="$category->name" required />
                    
                    <x-select name="parent_id" label="Parent Category" :options="$parentCategories->pluck('name', 'id')->toArray()" :selected="$category->parent_id" placeholder="None (Top Level)" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $category->description }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-button type="ghost" href="{{ route('admin.inventory.categories.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Update Category</x-button>
                </div>
            </x-card>
        </div>
    </form>
</x-layouts.admin>
