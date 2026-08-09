<x-layouts.admin title="Help Center">
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
        </div>
        <a href="{{ route('admin.documentation-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Back to Categories</a>
    </div>

    <form action="{{ route('admin.documentation-categories.update', $documentationCategory) }}" method="POST" class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="name" label="Category Name" required value="{{ $documentationCategory->name }}" />
                <x-input name="slug" label="URL Slug" required value="{{ $documentationCategory->slug }}" hint="Must be unique, lowercase, no spaces." />
            </div>

            <x-textarea name="icon" label="Icon (SVG string)" hint="Optional SVG icon code." value="{{ $documentationCategory->icon }}" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="order" type="number" label="Display Order" required value="{{ $documentationCategory->order }}" />
                <x-select name="is_active" label="Status" required>
                    <option value="1" @if($documentationCategory->is_active) selected @endif>Active</option>
                    <option value="0" @if(!$documentationCategory->is_active) selected @endif>Inactive</option>
                </x-select>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <x-button type="submit">Update Category</x-button>
        </div>
    </form>
</div>
</x-layouts.admin>

