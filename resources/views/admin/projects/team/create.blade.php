<x-layouts.admin title="Assign Team Member">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
            ];
            
            if ($selectedProject) {
                $breadcrumbs[] = ['label' => $selectedProject->name, 'url' => route('admin.projects.show', $selectedProject)];
            } else {
                $breadcrumbs[] = ['label' => 'Project Teams', 'url' => route('admin.projects.team.index')];
            }
            
            $breadcrumbs[] = ['label' => 'Assign Member'];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Assign Team Member</h1>
            <p class="mt-1 text-sm text-gray-500">Add a new member to a project team.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button type="default" href="{{ $selectedProject ? route('admin.projects.show', $selectedProject) : route('admin.projects.team.index') }}">
                Cancel
            </x-button>
        </div>
    </div>

    <div class="max-w-4xl">
        <x-card>
            <form action="{{ route('admin.projects.team.store') }}" method="POST" x-data="{ allocation: 100 }">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Project Selection --}}
                    <div class="md:col-span-2">
                        @if($selectedProject)
                            <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                <div class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2 text-gray-700">
                                    {{ $selectedProject->name }}
                                </div>
                            </div>
                        @else
                            <x-select name="project_id" label="Project" :options="$projects->pluck('name', 'id')->toArray()" :selected="old('project_id')" required />
                        @endif
                    </div>

                    {{-- User Selection --}}
                    <div>
                        <x-select name="user_id" label="User" :options="$users->pluck('first_name', 'id')->map(function($name, $id) use ($users) { $u = $users->firstWhere('id', $id); return $u->first_name . ' ' . $u->last_name; })->toArray()" :selected="old('user_id')" required />
                    </div>

                    {{-- Department Selection --}}
                    <div>
                        <x-select name="department_id" label="Department" :options="$departments->pluck('name', 'id')->toArray()" :selected="old('department_id')" required />
                    </div>

                    {{-- Role Selection --}}
                    <div>
                        <x-select name="project_role" label="Project Role" :options="[
                            'Project Manager' => 'Project Manager',
                            'Assistant Project Manager' => 'Assistant Project Manager',
                            'Architect' => 'Architect',
                            'Engineer' => 'Engineer',
                            'Site Engineer' => 'Site Engineer',
                            'Civil Engineer' => 'Civil Engineer',
                            'Electrical Engineer' => 'Electrical Engineer',
                            'Mechanical Engineer' => 'Mechanical Engineer',
                            'Quantity Surveyor' => 'Quantity Surveyor',
                            'Procurement Officer' => 'Procurement Officer',
                            'Accountant' => 'Accountant',
                            'HR Representative' => 'HR Representative',
                            'Quality Controller' => 'Quality Controller',
                            'Safety Officer' => 'Safety Officer',
                            'Supervisor' => 'Supervisor',
                            'Foreman' => 'Foreman',
                            'Technician' => 'Technician',
                            'Viewer' => 'Viewer',
                            'Administrator' => 'Administrator'
                        ]" :selected="old('project_role')" required />
                    </div>

                    {{-- Joined Date --}}
                    <div>
                        <x-input type="date" name="joined_at" label="Join Date" value="{{ old('joined_at', now()->format('Y-m-d')) }}" required />
                    </div>

                    {{-- Allocation --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allocation Percentage: <span x-text="allocation"></span>%</label>
                        <input type="range" name="allocation_percentage" min="1" max="100" x-model="allocation" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        @error('allocation_percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hourly Rate --}}
                    <div>
                        <x-input type="number" name="hourly_rate" label="Hourly Rate (Optional)" placeholder="0.00" step="0.01" value="{{ old('hourly_rate') }}" />
                    </div>
                    
                    {{-- Status --}}
                    <div>
                        <x-select name="status" label="Status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" :selected="old('status', 'Active')" required />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <x-button type="default" href="{{ $selectedProject ? route('admin.projects.show', $selectedProject) : route('admin.projects.team.index') }}">Cancel</x-button>
                    <x-button type="primary" submit>Assign Member</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>
