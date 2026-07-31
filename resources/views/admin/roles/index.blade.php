<x-layouts.admin title="Roles Management">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Roles Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage system roles and their permissions.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('role.create')
                <x-button type="a" href="{{ route('admin.roles.create') }}" color="primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Role
                </x-button>
            @endcan
        </div>
    </div>

    <x-card>
        <x-slot:header>
            <form action="{{ route('admin.roles.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center w-full">
                <div class="flex-1 w-full relative">
                    <x-input name="search" value="{{ request('search') }}" placeholder="Search roles by name..." class="pl-10" />
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-select name="guard_name" onchange="this.form.submit()">
                        <option value="">All Guards</option>
                        <option value="web" {{ request('guard_name') === 'web' ? 'selected' : '' }}>Web</option>
                        <option value="api" {{ request('guard_name') === 'api' ? 'selected' : '' }}>API</option>
                    </x-select>
                </div>
                <div>
                    <x-button type="submit" color="secondary">Filter</x-button>
                    @if(request()->hasAny(['search', 'guard_name']))
                        <a href="{{ route('admin.roles.index') }}" class="ml-2 text-sm text-blue-600 hover:text-blue-800">Clear</a>
                    @endif
                </div>
            </form>
        </x-slot:header>

        <x-table>
            <x-slot:head>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </x-slot:head>

            @forelse($roles as $role)
                <tr class="group hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge type="info">{{ $role->guard_name }}</x-badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate">
                            {{ $role->permissions->count() }} permissions
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $role->created_at->format('M j, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <div class="flex justify-end items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @can('role.view')
                            <a href="{{ route('admin.roles.show', $role) }}" title="View" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        @endcan
                        
                        @can('role.update')
                            @if(!in_array($role->name, ['Super Admin']))
                                <a href="{{ route('admin.roles.edit', $role) }}" title="Edit" class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            @endif
                        @endcan

                        @can('role.delete')
                            @if(!in_array($role->name, ['Super Admin', 'Company Admin']))
                                <button type="button" @click="$dispatch('open-modal', 'delete-role-{{ $role->id }}')" title="Delete" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @endif
                        @endcan
                    </div>
                    </td>
                </tr>
                {{-- Delete Modal --}}
                @can('role.delete')
                    @if(!in_array($role->name, ['Super Admin', 'Company Admin']))
                        <x-modal id="delete-role-{{ $role->id }}" maxWidth="md">
                            <x-slot:header>Delete Role</x-slot:header>
                            <div class="text-center py-4">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $role->name }}"?</h3>
                                <p class="text-sm text-gray-500">This action cannot be undone. Users with this role may lose access.</p>
                            </div>
                            <x-slot:footer>
                                <x-button type="ghost" @click="$dispatch('close-modal', 'delete-role-{{ $role->id }}')">Cancel</x-button>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="danger" submit>Delete Role</x-button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    @endif
                @endcan
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                        No roles found.
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if($roles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $roles->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
