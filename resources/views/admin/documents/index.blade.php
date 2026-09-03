<x-layouts.admin title="Documents">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Documents'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documents</h1>
            <p class="mt-1 text-sm text-gray-500">Centralized document management system.</p>
        </div>
        @can('create', App\Models\Document::class)
            <x-button type="primary" href="{{ route('admin.documents.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Document
            </x-button>
        @endcan
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input name="search" placeholder="Search documents..." :value="request('search')" :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'" />
            
            <x-select name="category_id" placeholder="All Categories" :options="$categories->pluck('name', 'id')" :selected="request('category_id')" />
            
            <x-select name="visibility" placeholder="Any Visibility" :options="['Private' => 'Private', 'Internal' => 'Internal', 'Public' => 'Public']" :selected="request('visibility')" />

            <div class="flex items-end gap-2">
                <x-button type="primary" size="md">Filter</x-button>
                @if(request()->hasAny(['search', 'category_id', 'visibility']))
                    <x-button type="ghost" href="{{ route('admin.documents.index') }}" size="md">Clear</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">File</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Category</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Size</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Visibility</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden xl:table-cell">Uploaded Date</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($documents as $document)
            <tr @class(['bg-red-50/30' => $document->trashed()])>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <a href="{{ route('admin.documents.show', $document) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                                {{ Str::limit($document->original_name, 30) }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5">v{{ $document->version }} • {{ strtoupper($document->extension) }}</p>
                        </div>
                    </div>
                </td>
                
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="text-sm text-gray-600">{{ $document->category->name ?? 'Uncategorized' }}</span>
                </td>

                <td class="px-4 py-3 hidden lg:table-cell">
                    <span class="text-sm text-gray-600 font-mono">{{ $document->formatted_size }}</span>
                </td>

                <td class="px-4 py-3">
                    <x-badge :type="match($document->visibility) { 'Public' => 'success', 'Internal' => 'info', 'Private' => 'warning', default => 'default' }">
                        {{ $document->visibility }}
                    </x-badge>
                </td>

                <td class="px-4 py-3 hidden xl:table-cell">
                    <span class="text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</span>
                </td>

                <td class="px-4 py-3 text-right">
                    <x-action-dropdown>
                        @can('view', $document)
                            <x-action-dropdown-item href="{{ route('admin.documents.show', $document) }}" icon="view">
                                View Document
                            </x-action-dropdown-item>
                        @endcan
                        @can('download', $document)
                            <x-action-dropdown-item href="{{ route('admin.documents.download', $document) }}">
                                Download File
                            </x-action-dropdown-item>
                        @endcan
                        @can('update', $document)
                            <x-action-dropdown-item href="{{ route('admin.documents.edit', $document) }}" icon="edit">
                                Edit Document
                            </x-action-dropdown-item>
                        @endcan
                        @can('delete', $document)
                            <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-document-{{ $document->id }}')" icon="delete" variant="danger">
                                Delete Document
                            </x-action-dropdown-item>
                        @endcan
                    </x-action-dropdown>
                </td>
            </tr>

            @can('delete', $document)
                <x-modal id="delete-document-{{ $document->id }}" maxWidth="md">
                    <x-slot:header>Delete Document</x-slot:header>
                    <div class="text-center py-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ Str::limit($document->original_name, 30) }}"?</h3>
                        <p class="text-sm text-gray-500">This action will soft-delete the document.</p>
                    </div>
                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Delete</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endcan
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                    No documents found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $documents->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
