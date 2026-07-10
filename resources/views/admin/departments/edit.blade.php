<x-layouts.admin title="Edit Department">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Departments', 'url' => route('admin.departments.index')],
                ['label' => $department->name, 'url' => route('admin.departments.show', $department)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-lg font-bold shadow-sm">
                    {{ strtoupper(substr($department->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit {{ $department->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Update department information and settings.</p>
                </div>
            </div>
            <x-button type="ghost" href="{{ route('admin.departments.show', $department) }}" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Details
            </x-button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.departments.update', $department) }}"
          x-data="{
              companyId: '{{ old('company_id', $department->company_id) }}',
              branchId: '{{ old('branch_id', $department->branch_id) }}',
              filteredBranches: {},
              init() {
                  this.fetchBranches();
              },
              async fetchBranches() {
                  if (!this.companyId) {
                      this.filteredBranches = {};
                      return;
                  }
                  try {
                      const response = await fetch(`/admin/departments/branches/${this.companyId}`);
                      this.filteredBranches = await response.json();
                  } catch (e) {
                      this.filteredBranches = {};
                  }
              }
          }">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- Organization --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Organization</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Company (with Alpine.js binding) --}}
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <select name="company_id" id="company_id"
                                x-model="companyId"
                                @change="branchId = ''; fetchBranches()"
                                class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select a company</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $department->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Branch (dependent dropdown) --}}
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                        <select name="branch_id" id="branch_id"
                                x-model="branchId"
                                class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select a branch</option>
                            <template x-for="(name, id) in filteredBranches" :key="id">
                                <option :value="id" x-text="name" :selected="id == branchId"></option>
                            </template>
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Department Details --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Department Details</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="name" label="Department Name" placeholder="e.g. Engineering" required :value="$department->name" />
                    <x-input name="code" label="Department Code" placeholder="DEPT-ENG" required :value="$department->code" />
                    <x-input name="manager_name" label="Manager Name" placeholder="Full name of the department manager" :value="$department->manager_name" />
                    <x-select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="old('status', $department->status)" />
                </div>
            </x-card>

            {{-- Contact Information --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="email" label="Email" type="email" placeholder="department@company.com" :value="$department->email" />
                    <x-input name="phone" label="Phone" placeholder="+971 4 123 4567" :value="$department->phone" />
                </div>
            </x-card>

            {{-- Description --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                </x-slot:header>

                <div>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        placeholder="Describe the purpose and responsibilities of this department..."
                        class="block w-full rounded-lg border border-gray-300 shadow-sm text-sm py-2.5 px-3 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                    >{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pb-6">
                <x-button type="ghost" href="{{ route('admin.departments.show', $department) }}">Cancel</x-button>
                <x-button type="primary" submit>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Department
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.admin>
