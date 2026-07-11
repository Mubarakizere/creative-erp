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
                <tr class="hover:bg-gray-50 transition-colors">
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
                        @can('role.view')
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        @endcan
                        
                        @can('role.update')
                            @if(!in_array($role->name, ['Super Admin']))
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-amber-600 hover:text-amber-900">Edit</a>
                            @endif
                        @endcan

                        @can('role.delete')
                            @if(!in_array($role->name, ['Super Admin', 'Company Admin']))
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900">Delete</button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
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
