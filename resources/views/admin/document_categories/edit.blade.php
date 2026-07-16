<x-layouts.admin title="Edit Document Category">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Document Categories', 'url' => route('admin.document-categories.index')],
                ['label' => $documentCategory->name, 'url' => route('admin.document-categories.show', $documentCategory)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            <p class="mt-1 text-sm text-gray-500">Update details for {{ $documentCategory->name }}.</p>
        </div>

        <form action="{{ route('admin.document-categories.update', $documentCategory) }}" method="POST">
            @csrf
            @method('PUT')
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="name" label="Category Name" value="{{ old('name', $documentCategory->name) }}" required />
                    <x-input name="color" label="Color Hex Code" value="{{ old('color', $documentCategory->color) }}" placeholder="e.g. #3b82f6" />
                    
                    <div class="md:col-span-2">
                        <x-textarea name="description" label="Description" rows="3">{{ old('description', $documentCategory->description) }}</x-textarea>
                    </div>

                    <x-input name="icon" label="SVG Icon" value="{{ old('icon', $documentCategory->icon) }}" class="md:col-span-2" />
                    
                    <x-input name="sort_order" label="Sort Order" type="number" value="{{ old('sort_order', $documentCategory->sort_order) }}" />
                    
                    <div class="flex items-center mt-6 hidden">
                        <input type="hidden" name="is_active" value="0">
                    </div>

                    <div class="flex items-center mt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $documentCategory->is_active))>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <x-button type="ghost" href="{{ route('admin.document-categories.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Save Changes</x-button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.admin>
