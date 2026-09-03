<x-layouts.admin title="Create Role">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Roles', 'url' => route('admin.roles.index')],
                ['label' => 'Create Role'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Access Role</h1>
        <p class="mt-1 text-sm text-slate-500 font-medium">Define new security role and configure system module permissions.</p>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <x-card title="Role Identification" description="Basic name and authentication guard scope.">
                    <div class="space-y-4">
                        <x-input name="name" label="Role Name" value="{{ old('name') }}" placeholder="e.g. Finance Officer, Site Engineer" required />

                        <x-select name="guard_name" label="Guard Name" required>
                            <option value="web" @selected(old('guard_name') === 'web')>Web (Standard UI)</option>
                            <option value="api" @selected(old('guard_name') === 'api')>API (Mobile / Rest)</option>
                        </x-select>
                    </div>
                </x-card>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <x-card title="Permissions Configuration" description="Enable access rights for each operational module.">
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Module Permissions</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Toggle access capabilities per module.</p>
                            </div>
                            <button type="button" onclick="document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true)" class="text-xs font-bold text-blue-600 hover:underline">Select All</button>
                        </div>
                    </x-slot:header>

                    <div class="space-y-6">
                        @foreach($permissionsGrouped as $module => $permissions)
                            <div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $module }} Permissions</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    @foreach($permissions as $permission)
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                class="permission-checkbox w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500"
                                                @checked(is_array(old('permissions')) && in_array($permission->name, old('permissions')))>
                                            <span>{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        
                        @error('permissions') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <x-slot:footer>
                        <a href="{{ route('admin.roles.index') }}">
                            <x-button type="ghost">Cancel</x-button>
                        </a>
                        <x-button type="primary" submit>Create Role</x-button>
                    </x-slot:footer>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.admin>
