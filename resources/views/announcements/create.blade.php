<x-layouts.admin title="Create Announcement">
    @can('create', App\Models\Announcement::class)
    <div class="mb-8">
        <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Announcements
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Announcement</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Broadcast a new message to the enterprise.</p>
    </div>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Announcement Details</h3>
            </div>
            
            <div class="p-6 space-y-6" x-data="audienceSelector()">
                <div>
                    <x-input name="title" label="Title" :value="old('title')" required />
                </div>

                <div>
                    <x-textarea name="content" label="Content" :value="old('content')" rows="5" required />
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <div>
                        <x-select name="category" label="Category" required>
                            <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>Information</option>
                            <option value="warning" {{ old('category') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="success" {{ old('category') == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="error" {{ old('category') == 'error' ? 'selected' : '' }}>Error</option>
                        </x-select>
                    </div>

                    <div>
                        <x-select name="priority" label="Priority" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </x-select>
                    </div>

                    <div class="col-span-1 sm:col-span-2 p-5 bg-blue-50/30 rounded-xl border border-blue-100/50 mt-2">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 tracking-tight">Target Audience</h4>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
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
                    </div>

                    <div>
                        <x-input type="datetime-local" name="starts_at" label="Starts At (Optional)" :value="old('starts_at')" />
                    </div>

                    <div>
                        <x-input type="datetime-local" name="expires_at" label="Expires At (Optional)" :value="old('expires_at')" />
                    </div>
                </div>
            </div>

            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input id="is_pinned" name="is_pinned" type="checkbox" value="1" {{ old('is_pinned') ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-amber-500 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition-colors">
                        <label for="is_pinned" class="ml-2 block text-sm font-bold text-gray-900">Pin Announcement</label>
                    </div>

                    <div class="flex items-center">
                        <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published') ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-emerald-500 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 transition-colors">
                        <label for="is_published" class="ml-2 block text-sm font-bold text-gray-900">Publish Immediately</label>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Create Announcement</button>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create announcements.</p>
        <div class="mt-6">
            <a href="{{ route('admin.announcements.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Announcements</a>
        </div>
    </div>
    @endcan

  @push('scripts')
  @php
      $initialUsers = [];
      if (old('user_ids')) {
          $initialUsers = \App\Models\User::whereIn('id', old('user_ids'))
              ->get(['id', 'first_name', 'last_name', 'email'])
              ->map(fn($u) => ['id' => $u->id, 'name' => $u->first_name . ' ' . $u->last_name, 'email' => $u->email])
              ->toArray();
      }
  @endphp
  <script>
      document.addEventListener('alpine:init', () => {
          Alpine.data('audienceSelector', () => ({
              type: '{{ old('audience_type', 'entire_system') }}',
              companyId: '{{ old('audience_company', '') }}',
              branchId: '{{ old('audience_branch', '') }}',
              departmentId: '{{ old('audience_department', '') }}',
              roleId: '{{ old('audience_role', '') }}',
              
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
