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

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Milestone</h1>
        <p class="mt-1 text-sm text-gray-500">Add a new milestone to a project.</p>
    </div>

    <form method="POST" action="{{ route('admin.milestones.store') }}">
        @csrf
        <x-card>
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

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="ghost" href="{{ route('admin.milestones.index') }}">Cancel</x-button>
                <x-button type="primary" submit>Create Milestone</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
