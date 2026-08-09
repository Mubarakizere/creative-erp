<x-layouts.admin title="Help Center">
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Category</h1>
        </div>
        <a href="{{ route('admin.documentation-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Back to Categories</a>
    </div>

    <form action="{{ route('admin.documentation-categories.store') }}" method="POST" class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="name" label="Category Name" required placeholder="e.g. Getting Started" />
                <x-input name="slug" label="URL Slug" required placeholder="e.g. getting-started" hint="Must be unique, lowercase, no spaces." />
            </div>

            <x-textarea name="icon" label="Icon (SVG string)" placeholder="<svg>...</svg>" hint="Optional SVG icon code." />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="order" type="number" label="Display Order" required value="0" />
                <x-select name="is_active" label="Status" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-select>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <x-button type="submit">Create Category</x-button>
        </div>
    </form>
</div>
</x-layouts.admin>

