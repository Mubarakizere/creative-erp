<x-layouts.admin title="Role Details">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $role->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Role configuration and assigned permissions.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
                        @can('role.delete')
                <x-button type="button" color="danger" @click="$dispatch('open-modal', 'delete-role-{{ $role->id }}')">
                    Delete role
                </x-button>
            @endcan
            
            @can('role.update')
                @if(!in_array($role->name, ['Super Admin']))
                    <x-button type="a" href="{{ route('admin.roles.edit', $role) }}" color="secondary">Edit Role</x-button>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-medium text-gray-900">Role Information</h3>
                </x-slot:header>

                <dl class="space-y-4 text-sm text-gray-600">
                    <div>
                        <dt class="font-medium text-gray-900">ID</dt>
                        <dd class="mt-1">{{ $role->id }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Name</dt>
                        <dd class="mt-1">{{ $role->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Guard Name</dt>
                        <dd class="mt-1"><x-badge type="info">{{ $role->guard_name }}</x-badge></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Created At</dt>
                        <dd class="mt-1">{{ $role->created_at->format('F j, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Updated At</dt>
                        <dd class="mt-1">{{ $role->updated_at->format('F j, Y H:i:s') }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-medium text-gray-900">Assigned Permissions ({{ $role->permissions->count() }})</h3>
                </x-slot:header>

                @if($role->permissions->isEmpty())
                    <p class="text-sm text-gray-500 py-4">No permissions assigned to this role.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($role->permissions as $permission)
                            <div class="flex items-center gap-2 p-2 rounded bg-gray-50 border border-gray-100">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-gray-700 truncate" title="{{ $permission->name }}">{{ $permission->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
    {{-- Delete Modal --}}
    @can('role.delete')
        <x-modal id="delete-role-{{ $role->id }}" maxWidth="md">
            <x-slot:header>Delete role</x-slot:header>
            <div class="text-center py-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $role->name }}"?</h3>
                <p class="text-sm text-gray-500">This action cannot be undone. Users relying on this role might lose access.</p>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="$dispatch('close-modal', 'delete-role-{{ $role->id }}')">Cancel</x-button>
                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="danger" submit>Delete role</x-button>
                </form>
            </x-slot:footer>
        </x-modal>
    @endcan
</x-layouts.admin>
