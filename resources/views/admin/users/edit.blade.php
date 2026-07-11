<x-layouts.admin title="Edit User">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Users', 'url' => route('admin.users.index')],
                ['label' => $user->full_name, 'url' => route('admin.users.show', $user)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="mt-1 text-sm text-gray-500">Update information for {{ $user->full_name }}</p>
        </div>
        <x-button type="ghost" href="{{ route('admin.users.show', $user) }}" size="sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to User
        </x-button>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data"
          x-data="{
              companyId: '{{ old('company_id', $user->company_id) }}',
              branchId: '{{ old('branch_id', $user->branch_id) }}',
              filteredBranches: {{ Js::from($branches) }},
              filteredDepartments: {{ Js::from($departments) }},
              async fetchBranches() {
                  if (!this.companyId) { this.filteredBranches = []; this.filteredDepartments = []; return; }
                  try {
                      const res = await fetch(`/admin/users/branches/${this.companyId}`);
                      this.filteredBranches = await res.json();
                  } catch (e) { this.filteredBranches = []; }
              },
              async fetchDepartments() {
                  if (!this.branchId) { this.filteredDepartments = []; return; }
                  try {
                      const res = await fetch(`/admin/users/departments/${this.branchId}`);
                      this.filteredDepartments = await res.json();
                  } catch (e) { this.filteredDepartments = []; }
              },
              avatarPreview: '{{ $user->avatar ? Storage::url($user->avatar) : null }}',
              handleFileChange(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.avatarPreview = URL.createObjectURL(file);
                  }
              }
          }">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- Organization Setup --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Organization</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <select name="company_id" id="company_id" x-model="companyId" @change="fetchBranches()" required
                                class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                        <select name="branch_id" id="branch_id" x-model="branchId" @change="fetchDepartments()" required
                                class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a branch</option>
                            <template x-for="branch in filteredBranches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name" :selected="branch.id == '{{ old('branch_id', $user->branch_id) }}'"></option>
                            </template>
                        </select>
                        @error('branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="department_id" required
                                class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a department</option>
                            <template x-for="dept in filteredDepartments" :key="dept.id">
                                <option :value="dept.id" x-text="dept.name" :selected="dept.id == '{{ old('department_id', $user->department_id) }}'"></option>
                            </template>
                        </select>
                        @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-card>

            {{-- Personal Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="first_name" label="First Name" :value="$user->first_name" required />
                    <x-input name="last_name" label="Last Name" :value="$user->last_name" required />
                    <x-input name="email" type="email" label="Email Address" :value="$user->email" required />
                    <x-input name="phone" label="Phone Number" :value="$user->phone" />
                    <x-input name="job_title" label="Job Title" :value="$user->job_title" />
                    
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <div class="flex items-center gap-4 mt-2">
                            <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <div class="h-full w-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                        {{ $user->initials }}
                                    </div>
                                </template>
                            </div>
                            <div>
                                <input type="file" name="avatar" accept="image/*" @change="handleFileChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current photo.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Access & Security --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Access & Security</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Roles <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 border rounded-lg p-4 bg-gray-50">
                            @php $userRoles = $user->roles->pluck('name')->toArray(); @endphp
                            @foreach($roles as $role)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" @checked(in_array($role->name, old('roles', $userRoles)))>
                                    <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-6">
                        <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'locked' => 'Locked']" :selected="$user->status" />
                        
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <h4 class="text-sm font-medium text-yellow-800 mb-3">Change Password</h4>
                            <div class="space-y-4">
                                <x-input name="password" type="password" label="New Password" placeholder="Leave blank to keep current" />
                                <x-input name="password_confirmation" type="password" label="Confirm Password" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end gap-3 pb-6">
                <x-button type="ghost" href="{{ route('admin.users.show', $user) }}">Cancel</x-button>
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update User
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
