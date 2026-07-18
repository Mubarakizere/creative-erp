<x-layouts.admin title="Edit Announcement">
 <div class="mb-6">
 <h1 class="text-2xl font-semibold text-gray-900 ">Edit Announcement</h1>
 </div>

 <x-card>
 <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
 @csrf
 @method('PUT')

 <div class="space-y-6" x-data="audienceSelector()">
 <div>
 <x-input name="title" label="Title" :value="old('title', $announcement->title)" required />
 </div>

 <div>
 <x-textarea name="content" label="Content" :value="old('content', $announcement->content)" rows="5" required />
 </div>

 <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
 <div>
 <x-select name="category" label="Category" required>
 <option value="info" {{ old('category', $announcement->category) == 'info' ? 'selected' : '' }}>Information</option>
 <option value="warning" {{ old('category', $announcement->category) == 'warning' ? 'selected' : '' }}>Warning</option>
 <option value="success" {{ old('category', $announcement->category) == 'success' ? 'selected' : '' }}>Success</option>
 <option value="error" {{ old('category', $announcement->category) == 'error' ? 'selected' : '' }}>Error</option>
 </x-select>
 </div>

 <div>
 <x-select name="priority" label="Priority" required>
 <option value="low" {{ old('priority', $announcement->priority) == 'low' ? 'selected' : '' }}>Low</option>
 <option value="normal" {{ old('priority', $announcement->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
 <option value="high" {{ old('priority', $announcement->priority) == 'high' ? 'selected' : '' }}>High</option>
 <option value="urgent" {{ old('priority', $announcement->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
 </x-select>
 </div>

 <div class="col-span-1 sm:col-span-2 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
     <div class="sm:col-span-2">
         <x-select name="audience_type" label="Audience Type" x-model="type" required>
             <option value="entire_system">Entire System</option>
             <option value="company">Company</option>
             <option value="branch">Branch</option>
             <option value="department">Department</option>
             <option value="role">Role</option>
             <option value="specific_users">Specific Users</option>
         </x-select>
     </div>

     <!-- Dynamic Fields based on Audience Type -->
     <template x-if="['company', 'branch', 'department'].includes(type)">
         <div>
             <x-select name="audience_company" label="Select Company" x-model="companyId" :required="true">
                 <option value="">-- Choose Company --</option>
                 <template x-for="company in companies" :key="company.id">
                     <option :value="company.id" x-text="company.name"></option>
                 </template>
             </x-select>
         </div>
     </template>

     <template x-if="['branch', 'department'].includes(type)">
         <div>
             <x-select name="audience_branch" label="Select Branch" x-model="branchId" :required="true">
                 <option value="">-- Choose Branch --</option>
                 <template x-for="branch in filteredBranches" :key="branch.id">
                     <option :value="branch.id" x-text="branch.name"></option>
                 </template>
             </x-select>
         </div>
     </template>

     <template x-if="type === 'department'">
         <div>
             <x-select name="audience_department" label="Select Department" x-model="departmentId" :required="true">
                 <option value="">-- Choose Department --</option>
                 <template x-for="dept in filteredDepartments" :key="dept.id">
                     <option :value="dept.id" x-text="dept.name"></option>
                 </template>
             </x-select>
         </div>
     </template>

     <template x-if="type === 'role'">
         <div>
             <x-select name="audience_role" label="Select Role" x-model="roleId" :required="true">
                 <option value="">-- Choose Role --</option>
                 <template x-for="role in roles" :key="role.id">
                     <option :value="role.id" x-text="role.name"></option>
                 </template>
             </x-select>
         </div>
     </template>

     <template x-if="type === 'specific_users'">
         <div class="sm:col-span-2" wire:ignore>
             <label class="block text-sm font-medium text-gray-700 mb-1">Select Users</label>
             <select id="user-select" name="user_ids[]" multiple placeholder="Search for users..."></select>
             <p class="mt-1 text-xs text-gray-500">Search by name or email.</p>
             @error('user_ids')
                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
             @enderror
         </div>
     </template>

     <!-- Hidden Audience ID -->
     <input type="hidden" name="audience_id" :value="audienceId">
 </div>

 <div>
 <x-input type="datetime-local" name="starts_at" label="Starts At" :value="old('starts_at', $announcement->starts_at ? $announcement->starts_at->format('Y-m-d\TH:i') : '')" />
 </div>

 <div>
 <x-input type="datetime-local" name="expires_at" label="Expires At" :value="old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '')" />
 </div>
 </div>

 <div class="flex items-center space-x-6">
 <div class="flex items-center">
 <input id="is_pinned" name="is_pinned" type="checkbox" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
 <label for="is_pinned" class="ml-2 block text-sm text-gray-900 ">Pin Announcement</label>
 </div>

 <div class="flex items-center">
 <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
 <label for="is_published" class="ml-2 block text-sm text-gray-900 ">Publish Immediately</label>
 </div>
 </div>
 </div>

 <div class="mt-6 flex items-center justify-end space-x-3">
 <a href="{{ route('admin.announcements.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
 <x-button type="primary" submit>Update Announcement</x-button>
 </div>
  </form>
  </x-card>

  @push('scripts')
  @php
      $initialUsers = [];
      if (old('user_ids')) {
          $initialUsers = \App\Models\User::whereIn('id', old('user_ids'))
              ->get(['id', 'first_name', 'last_name', 'email'])
              ->map(fn($u) => ['id' => $u->id, 'name' => $u->first_name . ' ' . $u->last_name, 'email' => $u->email])
              ->toArray();
      } elseif (isset($announcement) && $announcement->users) {
          $initialUsers = $announcement->users
              ->map(fn($u) => ['id' => $u->id, 'name' => $u->first_name . ' ' . $u->last_name, 'email' => $u->email])
              ->toArray();
      }
  @endphp
  <script>
      document.addEventListener('alpine:init', () => {
          Alpine.data('audienceSelector', () => ({
              type: '{{ old('audience_type', $announcement->audience_type) }}',
              companyId: '{{ old('audience_company', $announcement->audience_type === 'company' ? $announcement->audience_id : '') }}',
              branchId: '{{ old('audience_branch', $announcement->audience_type === 'branch' ? $announcement->audience_id : '') }}',
              departmentId: '{{ old('audience_department', $announcement->audience_type === 'department' ? $announcement->audience_id : '') }}',
              roleId: '{{ old('audience_role', $announcement->audience_type === 'role' ? $announcement->audience_id : '') }}',
              
              companies: @json($companies ?? []),
              branches: @json($branches ?? []),
              departments: @json($departments ?? []),
              roles: @json($roles ?? []),
              
              initialUsers: @json($initialUsers),
              
              tomSelect: null,

              init() {
                  this.$watch('type', (value) => {
                      if (value === 'specific_users') {
                          this.$nextTick(() => {
                              this.initTomSelect();
                          });
                      } else if (this.tomSelect) {
                          this.tomSelect.destroy();
                          this.tomSelect = null;
                      }
                  });

                  if (this.type === 'specific_users') {
                      this.$nextTick(() => {
                          this.initTomSelect();
                      });
                  }
              },

              get filteredBranches() {
                  return this.branches.filter(b => b.company_id == this.companyId);
              },

              get filteredDepartments() {
                  return this.departments.filter(d => d.branch_id == this.branchId);
              },

              get audienceId() {
                  if (this.type === 'company') return this.companyId;
                  if (this.type === 'branch') return this.branchId;
                  if (this.type === 'department') return this.departmentId;
                  if (this.type === 'role') return this.roleId;
                  return '';
              },

              initTomSelect() {
                  const el = document.getElementById('user-select');
                  if (!el) return;
                  
                  if (!window.TomSelect) {
                      console.error('TomSelect is not loaded');
                      return;
                  }

                  this.tomSelect = new window.TomSelect(el, {
                      valueField: 'id',
                      labelField: 'name',
                      searchField: ['name', 'email'],
                      options: this.initialUsers,
                      items: this.initialUsers.map(u => u.id),
                      load: function(query, callback) {
                          if (!query.length) return callback();
                          fetch(`{{ route('admin.users.search') }}?q=${encodeURIComponent(query)}`)
                              .then(response => response.json())
                              .then(json => {
                                  callback(json);
                              }).catch(()=>{
                                  callback();
                              });
                      },
                      render: {
                          option: function(item, escape) {
                              return `<div>
                                  <span class="block font-medium">${escape(item.name)}</span>
                                  <span class="block text-sm text-gray-500">${escape(item.email)}</span>
                              </div>`;
                          },
                          item: function(item, escape) {
                              return `<div>${escape(item.name)}</div>`;
                          }
                      }
                  });
              }
          }));
      });
  </script>
  @endpush
</x-layouts.admin>
