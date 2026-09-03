<x-layouts.admin title="Help Center">
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documentation Categories</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the categories shown in the Help Center.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.documentation.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                View Help Center
            </a>
            <a href="{{ route('admin.documentation-categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Category
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <x-table>
            <x-slot name="head">
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
            </x-slot>
            <x-slot name="body">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($category->icon)
                                    <div class="text-gray-400 w-5 h-5">{!! $category->icon !!}</div>
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <x-action-dropdown>
                                @can('update', $category)
                                    <x-action-dropdown-item href="{{ route('admin.documentation-categories.edit', $category) }}" icon="edit">
                                        Edit Category
                                    </x-action-dropdown-item>
                                @endcan
                                @can('delete', $category)
                                    <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-doc-category-{{ $category->id }}')" icon="delete" variant="danger">
                                        Delete Category
                                    </x-action-dropdown-item>
                                @endcan
                            </x-action-dropdown>

                            @can('delete', $category)
                            <x-modal id="delete-doc-category-{{ $category->id }}" maxWidth="md">
                                <x-slot:header>Delete Documentation Category</x-slot:header>
                                <div class="text-center py-4">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $category->name }}"?</h3>
                                    <p class="text-sm text-gray-500">Are you sure you want to delete this category? All related articles will also be deleted.</p>
                                </div>
                                <x-slot:footer>
                                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                                    <form method="POST" action="{{ route('admin.documentation-categories.destroy', $category) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="danger" submit>Delete Category</x-button>
                                    </form>
                                </x-slot:footer>
                            </x-modal>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-table>
        
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
</x-layouts.admin>

