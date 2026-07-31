<x-layouts.admin title="Edit Team Member">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $teamMember->project->name, 'url' => route('admin.projects.show', $teamMember->project_id)],
                ['label' => 'Team', 'url' => route('admin.projects.team.index')],
                ['label' => $teamMember->user->full_name],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.projects.show', $teamMember->project_id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Project
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Team Member</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Update assignment details for {{ $teamMember->user->full_name }} on {{ $teamMember->project->name }}.</p>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Assignment Details</h3>
            </div>
            <form action="{{ route('admin.projects.team.update', $teamMember) }}" method="POST" id="team-form" x-data="{ allocation: {{ $teamMember->allocation_percentage }} }">
                @csrf
                @method('PUT')
                <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- User Display (Readonly) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <div class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700">
                            {{ $teamMember->user->full_name }}
                        </div>
                    </div>

                    {{-- Department Selection --}}
                    <div>
                        <x-select name="department_id" label="Department" :options="$departments->pluck('name', 'id')->toArray()" :selected="old('department_id', $teamMember->department_id)" required />
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
                        ]" :selected="old('project_role', $teamMember->project_role)" required />
                    </div>

                    {{-- Joined Date --}}
                    <div>
                        <x-input type="date" name="joined_at" label="Join Date" value="{{ old('joined_at', $teamMember->joined_at?->format('Y-m-d')) }}" required />
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
                        <x-input type="number" name="hourly_rate" label="Hourly Rate (Optional)" placeholder="0.00" step="0.01" value="{{ old('hourly_rate', $teamMember->hourly_rate) }}" />
                    </div>
                    
                    {{-- Status --}}
                    <div>
                        <x-select name="status" label="Status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" :selected="old('status', $teamMember->status)" required />
                    </div>
                    
                    {{-- Left Date --}}
                    <div>
                        <x-input type="date" name="left_at" label="Left Date (Optional)" value="{{ old('left_at', $teamMember->left_at?->format('Y-m-d')) }}" />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes', $teamMember->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                </div>
            </form>
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.projects.show', $teamMember->project_id) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    Cancel
                </a>
                <button type="submit" form="team-form" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    Update Member
                </button>
            </div>
        </div>
    </div>
</x-layouts.admin>
