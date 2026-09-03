<x-layouts.admin title="Permissions Management">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permissions Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage granular permissions across modules.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('permission.create')
                <x-button type="a" href="{{ route('admin.permissions.create') }}" color="primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Permission
                </x-button>
            @endcan
        </div>
    </div>

    <x-card>
        <x-slot:header>
            <form action="{{ route('admin.permissions.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center w-full">
                <div class="flex-1 w-full relative">
                    <x-input name="search" value="{{ request('search') }}" placeholder="Search permissions by name..." class="pl-10" />
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-input name="module" value="{{ request('module') }}" placeholder="Filter by module..." />
                </div>
                <div>
                    <x-button type="submit" color="secondary">Filter</x-button>
                    @if(request()->hasAny(['search', 'module']))
                        <a href="{{ route('admin.permissions.index') }}" class="ml-2 text-sm text-blue-600 hover:text-blue-800">Clear</a>
                    @endif
                </div>
            </form>
        </x-slot:header>

        <x-table>
            <x-slot:head>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </x-slot:head>

            @forelse($permissions as $permission)
                @php
                    $parts = explode('.', $permission->name);
                    $module = $parts[0] ?? 'general';
                @endphp
                <tr class="group hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 capitalize">{{ $module }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge type="info">{{ $permission->guard_name }}</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $permission->created_at->format('M j, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-action-dropdown>
                            @can('view', $permission)
                                <x-action-dropdown-item href="{{ route('admin.permissions.show', $permission) }}" icon="view">
                                    View Permission
                                </x-action-dropdown-item>
                            @endcan
                            @can('update', $permission)
                                <x-action-dropdown-item href="{{ route('admin.permissions.edit', $permission) }}" icon="edit">
                                    Edit Permission
                                </x-action-dropdown-item>
                            @endcan
                            @can('delete', $permission)
                                <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-permission-{{ $permission->id }}')" icon="delete" variant="danger">
                                    Delete Permission
                                </x-action-dropdown-item>
                            @endcan
                        </x-action-dropdown>
                    </td>
                </tr>
                {{-- Delete Modal --}}
                @can('permission.delete')
                    <x-modal id="delete-permission-{{ $permission->id }}" maxWidth="md">
                        <x-slot:header>Delete Permission</x-slot:header>
                        <div class="text-center py-4">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $permission->name }}"?</h3>
                            <p class="text-sm text-gray-500">This action cannot be undone. Users relying on this permission might lose access. This might break application logic.</p>
                        </div>
                        <x-slot:footer>
                            <x-button type="ghost" @click="$dispatch('close-modal', 'delete-permission-{{ $permission->id }}')">Cancel</x-button>
                            <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-button type="danger" submit>Delete Permission</x-button>
                            </form>
                        </x-slot:footer>
                    </x-modal>
                @endcan
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                        No permissions found.
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $permissions->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
