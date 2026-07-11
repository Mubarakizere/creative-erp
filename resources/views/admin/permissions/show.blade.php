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
</x-layouts.admin>
