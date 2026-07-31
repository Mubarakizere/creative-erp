<x-layouts.admin title="Create Milestone">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Milestones', 'url' => route('admin.milestones.index')],
                ['label' => 'Create Milestone'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Milestone::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.milestones.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Milestones
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Milestone</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Add a new milestone to a project.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.milestones.store') }}" id="milestone-form">
        @csrf
        
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Milestone Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-select name="project_id" label="Project" :options="$projects->pluck('name', 'id')->toArray()" :selected="old('project_id')" required />
                </div>
                
                <div class="sm:col-span-2">
                    <x-input name="name" label="Milestone Name" :value="old('name')" required />
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-select name="priority" label="Priority" :options="['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical']" :selected="old('priority', 'Medium')" required />
                </div>

                <div>
                    <x-select name="status" label="Status" :options="['Pending' => 'Pending', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'On Hold' => 'On Hold']" :selected="old('status', 'Pending')" required />
                </div>

                <div>
                    <x-input type="date" name="start_date" label="Start Date" :value="old('start_date')" />
                </div>

                <div>
                    <x-input type="date" name="due_date" label="Due Date" :value="old('due_date')" />
                </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.milestones.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Milestone
                </button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create milestones.</p>
        <div class="mt-6">
            <a href="{{ route('admin.milestones.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Milestones</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
