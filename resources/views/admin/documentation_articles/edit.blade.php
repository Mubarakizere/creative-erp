<x-layouts.admin title="Help Center">
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Article</h1>
        </div>
        <a href="{{ route('admin.documentation-articles.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Back to Articles</a>
    </div>

    <form action="{{ route('admin.documentation-articles.update', $documentationArticle) }}" method="POST" class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input name="title" label="Article Title" required value="{{ $documentationArticle->title }}" />
                <x-input name="slug" label="URL Slug" required value="{{ $documentationArticle->slug }}" hint="Must be unique, lowercase, no spaces." />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <x-select name="documentation_category_id" label="Category" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if($documentationArticle->documentation_category_id == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="md:col-span-1">
                    <x-input name="order" type="number" label="Display Order" required value="{{ $documentationArticle->order }}" />
                </div>
                <div class="md:col-span-1">
                    <x-select name="status" label="Status" required>
                        <option value="published" @if($documentationArticle->status == 'published') selected @endif>Published</option>
                        <option value="draft" @if($documentationArticle->status == 'draft') selected @endif>Draft</option>
                    </x-select>
                </div>
            </div>

            <div>
                <x-textarea name="content" label="Article Content (Markdown supported)" required rows="15" value="{{ $documentationArticle->content }}" hint="You can use Markdown formatting. E.g. # Heading, **bold**, - list items." />
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <x-button type="submit">Update Article</x-button>
        </div>
    </form>
</div>
</x-layouts.admin>

