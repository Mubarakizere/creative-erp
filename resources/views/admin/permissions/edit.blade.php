<x-layouts.admin title="Edit Permission">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.permissions.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Permission: {{ $permission->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Update permission details.</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')

            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-medium text-gray-900">Permission Details</h3>
                </x-slot:header>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permission Name</label>
                        <x-input name="name" value="{{ old('name', $permission->name) }}" required />
                        <p class="mt-1 text-xs text-gray-500">Warning: Changing the name might break existing code references.</p>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guard Name</label>
                        <x-select name="guard_name" required>
                            <option value="web" {{ old('guard_name', $permission->guard_name) === 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ old('guard_name', $permission->guard_name) === 'api' ? 'selected' : '' }}>API</option>
                        </x-select>
                        @error('guard_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-card>
            
            <div class="mt-6 flex justify-end gap-3">
                <x-button type="a" href="{{ route('admin.permissions.index') }}" color="secondary">Cancel</x-button>
                <x-button type="submit" color="primary">Update Permission</x-button>
            </div>
        </form>
    </div>
</x-layouts.admin>
