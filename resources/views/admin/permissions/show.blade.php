<x-layouts.admin title="Permission Details">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.permissions.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $permission->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Permission details and configuration.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
                        @can('permission.delete')
                <x-button type="button" color="danger" @click="$dispatch('open-modal', 'delete-permission-{{ $permission->id }}')">
                    Delete permission
                </x-button>
            @endcan
            
            @can('permission.update')
                <x-button type="a" href="{{ route('admin.permissions.edit', $permission) }}" color="secondary">Edit Permission</x-button>
            @endcan
        </div>
    </div>

    <div class="max-w-2xl">
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900">Permission Information</h3>
            </x-slot:header>

            <dl class="space-y-4 text-sm text-gray-600">
                <div>
                    <dt class="font-medium text-gray-900">ID</dt>
                    <dd class="mt-1">{{ $permission->id }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Name</dt>
                    <dd class="mt-1">{{ $permission->name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Module</dt>
                    @php
                        $parts = explode('.', $permission->name);
                        $module = $parts[0] ?? 'general';
                    @endphp
                    <dd class="mt-1 capitalize">{{ $module }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Guard Name</dt>
                    <dd class="mt-1"><x-badge type="info">{{ $permission->guard_name }}</x-badge></dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Roles Using This</dt>
                    <dd class="mt-1">
                        @if($permission->roles->isEmpty())
                            <span class="text-gray-400">None</span>
                        @else
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($permission->roles as $role)
                                    <x-badge type="success">{{ $role->name }}</x-badge>
                                @endforeach
                            </div>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Created At</dt>
                    <dd class="mt-1">{{ $permission->created_at->format('F j, Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-900">Updated At</dt>
                    <dd class="mt-1">{{ $permission->updated_at->format('F j, Y H:i:s') }}</dd>
                </div>
            </dl>
        </x-card>
    </div>
    {{-- Delete Modal --}}
    @can('permission.delete')
        <x-modal id="delete-permission-{{ $permission->id }}" maxWidth="md">
            <x-slot:header>Delete permission</x-slot:header>
            <div class="text-center py-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $permission->name }}"?</h3>
                <p class="text-sm text-gray-500">This action cannot be undone. Users relying on this permission might lose access.</p>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="$dispatch('close-modal', 'delete-permission-{{ $permission->id }}')">Cancel</x-button>
                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-button type="danger" submit>Delete permission</x-button>
                </form>
            </x-slot:footer>
        </x-modal>
    @endcan
</x-layouts.admin>
