<x-layouts.admin title="Create Role">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Role</h1>
                <p class="mt-1 text-sm text-gray-500">Add a new role and configure its permissions.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <x-card>
                    <x-slot:header>
                        <h3 class="text-lg font-medium text-gray-900">Role Details</h3>
                    </x-slot:header>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                            <x-input name="name" value="{{ old('name') }}" placeholder="e.g. Project Manager" required />
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guard Name</label>
                            <x-select name="guard_name" required>
                                <option value="web" {{ old('guard_name') === 'web' ? 'selected' : '' }}>Web</option>
                                <option value="api" {{ old('guard_name') === 'api' ? 'selected' : '' }}>API</option>
                            </x-select>
                            @error('guard_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="lg:col-span-2">
                <x-card>
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Permissions Configuration</h3>
                            <button type="button" onclick="document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Select All</button>
                        </div>
                    </x-slot:header>

                    <div class="space-y-6">
                        @foreach($permissionsGrouped as $module => $permissions)
                            <div>
                                <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-3">
                                    <h4 class="text-md font-semibold text-gray-800 capitalize">{{ $module }} Permissions</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($permissions as $permission)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                class="permission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        
                        @error('permissions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-card>
                
                <div class="mt-6 flex justify-end gap-3">
                    <x-button type="a" href="{{ route('admin.roles.index') }}" color="secondary">Cancel</x-button>
                    <x-button type="submit" color="primary">Save Role</x-button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
