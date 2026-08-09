<x-layouts.admin title="Help Center">
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documentation Articles</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the articles in the Help Center.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                View Help Center
            </a>
            <a href="{{ route('admin.documentation-articles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Article
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <x-table>
            <x-slot name="head">
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
            </x-slot>
            <x-slot name="body">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $article->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($article->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.documentation-articles.edit', $article) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('admin.documentation-articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No articles found.
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-table>
        
        @if($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
</x-layouts.admin>

