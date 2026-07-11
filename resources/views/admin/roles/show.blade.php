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
</x-layouts.admin>
