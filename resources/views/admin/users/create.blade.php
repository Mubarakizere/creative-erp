<x-layouts.admin title="Create User">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Users', 'url' => route('admin.users.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create User Account</h1>
        <p class="mt-1 text-sm text-slate-500 font-medium">Register a new system user and assign organizational permissions.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data"
          x-data="{
              companyId: '{{ old('company_id', '') }}',
              branchId: '{{ old('branch_id', '') }}',
              filteredBranches: [],
              filteredDepartments: [],
              init() {
                  if (this.companyId) this.fetchBranches();
                  if (this.branchId) this.fetchDepartments();
              },
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
              avatarPreview: null,
              handleFileChange(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.avatarPreview = URL.createObjectURL(file);
                  }
              }
          }">
        @csrf

        <div class="space-y-8">
            {{-- Organization Setup --}}
            <x-form-section 
                title="Organization Assignment" 
                description="Link user to target company, branch, and department structure.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <x-select 
                            label="Company" 
                            name="company_id" 
                            x-model="companyId" 
                            @change="fetchBranches()" 
                            required
                            placeholder="Select company..."
                        >
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </x-select>

                        <x-select 
                            label="Branch" 
                            name="branch_id" 
                            x-model="branchId" 
                            @change="fetchDepartments()" 
                            required
                            placeholder="Select branch..."
                        >
                            <template x-for="branch in filteredBranches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name" :selected="branch.id == '{{ old('branch_id') }}'"></option>
                            </template>
                        </x-select>

                        <x-select 
                            label="Department" 
                            name="department_id" 
                            required
                            placeholder="Select department..."
                        >
                            <template x-for="dept in filteredDepartments" :key="dept.id">
                                <option :value="dept.id" x-text="dept.name" :selected="dept.id == '{{ old('department_id') }}'"></option>
                            </template>
                        </x-select>
                    </div>
                </x-card>
            </x-form-section>

            {{-- Personal Information --}}
            <x-form-section 
                title="Personal Information" 
                description="User profile metadata, contact info, job title, and avatar photo.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-input name="first_name" label="First Name" required placeholder="e.g. John" />
                        <x-input name="last_name" label="Last Name" required placeholder="e.g. Doe" />
                        <x-input name="email" type="email" label="Email Address" required placeholder="john.doe@company.com" />
                        <x-input name="phone" label="Phone Number" placeholder="+250 788 000 000" />
                        <div class="md:col-span-2">
                            <x-input name="job_title" label="Job Title" placeholder="e.g. Senior Project Manager, Civil Engineer" />
                        </div>
                        
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Profile Photo</label>
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200 shrink-0">
                                    <template x-if="avatarPreview">
                                        <img :src="avatarPreview" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!avatarPreview">
                                        <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </template>
                                </div>
                                <div>
                                    <input type="file" name="avatar" accept="image/*" @change="handleFileChange" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                    <p class="text-[11px] text-slate-400 mt-1">PNG, JPG up to 2MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            </x-form-section>

            {{-- Access & Security --}}
            <x-form-section 
                title="Access & System Roles" 
                description="Assign role permissions and define initial login password credentials.">
                
                <x-card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">System Roles <span class="text-rose-500 font-bold">*</span></label>
                            <div class="grid grid-cols-2 gap-2.5 border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 max-h-56 overflow-y-auto scrollbar-thin">
                                @foreach($roles as $role)
                                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500" @checked(is_array(old('roles')) && in_array($role->name, old('roles')))>
                                        <span>{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-4">
                            <x-select name="status" label="Account Status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending']" selected="active" required />
                            
                            <x-input name="password" type="password" label="Password" required placeholder="••••••••" />
                            <x-input name="password_confirmation" type="password" label="Confirm Password" required placeholder="••••••••" />
                        </div>
                    </div>

                    <x-slot:footer>
                        <a href="{{ route('admin.users.index') }}">
                            <x-button type="ghost">Cancel</x-button>
                        </a>
                        <x-button type="primary" submit>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Create User Account
                        </x-button>
                    </x-slot:footer>
                </x-card>
            </x-form-section>
        </div>
    </form>
</x-layouts.admin>
