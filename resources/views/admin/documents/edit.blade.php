<x-layouts.admin title="Edit Document">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Documents', 'url' => route('admin.documents.index')],
                ['label' => Str::limit($document->original_name, 30), 'url' => route('admin.documents.show', $document)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Edit Document</h1>
            <p class="mt-1 text-sm text-gray-500">Update metadata or replace the file for {{ $document->original_name }}.</p>
        </div>

        <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="original_name" label="Document Name" value="{{ old('original_name', $document->original_name) }}" required />
                    
                    <x-select name="category_id" label="Category" :options="$categories->pluck('name', 'id')" :selected="old('category_id', $document->category_id)" />
                    
                    <x-select name="visibility" label="Visibility" :options="['Private' => 'Private', 'Internal' => 'Internal', 'Public' => 'Public']" :selected="old('visibility', $document->visibility)" />
                    
                    <div class="md:col-span-2">
                        <x-textarea name="description" label="Description" rows="3">{{ old('description', $document->description) }}</x-textarea>
                    </div>

                    <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Replace File (Optional, Max 100MB)</label>
                        <p class="text-xs text-gray-500 mb-2">Uploading a new file will automatically increment the version number.</p>
                        <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <x-button type="ghost" href="{{ route('admin.documents.show', $document) }}">Cancel</x-button>
                    <x-button type="primary" submit>Save Changes</x-button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.admin>
