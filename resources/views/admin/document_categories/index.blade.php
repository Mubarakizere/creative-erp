<x-layouts.admin title="Document Categories">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Document Categories'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Document Categories</h1>
                <p class="mt-1 text-sm text-gray-500">Manage categories for the document management system.</p>
            </div>
            @can('create', App\Models\DocumentCategory::class)
                <x-button type="primary" href="{{ route('admin.document-categories.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Category
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.document-categories.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-input
                name="search"
                placeholder="Search categories..."
                :value="request('search')"
                :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'"
            />
            
            <div class="flex items-end gap-2">
                <x-button type="primary" size="md">
                    Filter
                </x-button>
                @if(request('search'))
                    <x-button type="ghost" href="{{ route('admin.document-categories.index') }}" size="md">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Categories Table --}}
    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Description</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($categories as $category)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($category->icon)
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100" style="color: {{ $category->color ?? '#6b7280' }}">
                                {!! $category->icon !!}
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <a href="{{ route('admin.document-categories.show', $category) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                                {{ $category->name }}
                            </a>
                        </div>
                    </div>
                </td>
                
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500">
                    {{ Str::limit($category->description, 50) }}
                </td>

                <td class="px-4 py-3">
                    <x-badge :type="$category->is_active ? 'success' : 'default'">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </td>

                <td class="px-4 py-3 text-right">
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                        </button>
                        <div x-show="open" class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1" style="display: none;">
                            @can('view', $category)
                                <a href="{{ route('admin.document-categories.show', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View</a>
                            @endcan
                            @can('update', $category)
                                <a href="{{ route('admin.document-categories.edit', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Edit</a>
                            @endcan
                            @can('delete', $category)
                                <button @click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">Delete</button>
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
            
            @can('delete', $category)
                <x-modal id="delete-category-{{ $category->id }}" maxWidth="md">
                    <x-slot:header>Delete Category</x-slot:header>
                    <div class="text-center py-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $category->name }}"?</h3>
                        <p class="text-sm text-gray-500">This action will soft-delete the category.</p>
                    </div>
                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.document-categories.destroy', $category) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Delete</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endcan
        @empty
            <tr>
                <td colspan="4" class="px-4 py-12 text-center text-gray-500">
                    No categories found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $categories->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
