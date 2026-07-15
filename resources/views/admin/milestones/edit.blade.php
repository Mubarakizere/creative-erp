<x-layouts.admin title="Edit Milestone">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Milestones', 'url' => route('admin.milestones.index')],
                ['label' => $milestone->name, 'url' => route('admin.milestones.show', $milestone)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Milestone</h1>
        <p class="mt-1 text-sm text-gray-500">Update milestone details.</p>
    </div>

    <form method="POST" action="{{ route('admin.milestones.update', $milestone) }}">
        @csrf
        @method('PUT')
        <x-card>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input name="name" label="Milestone Name" :value="old('name', $milestone->name)" required />
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $milestone->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-select name="priority" label="Priority" :options="['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical']" :selected="old('priority', $milestone->priority)" required />
                </div>

                <div>
                    <x-select name="status" label="Status" :options="['Pending' => 'Pending', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'On Hold' => 'On Hold']" :selected="old('status', $milestone->status)" required />
                </div>

                <div>
                    <x-input type="date" name="start_date" label="Start Date" :value="old('start_date', optional($milestone->start_date)->format('Y-m-d'))" />
                </div>

                <div>
                    <x-input type="date" name="due_date" label="Due Date" :value="old('due_date', optional($milestone->due_date)->format('Y-m-d'))" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="ghost" href="{{ route('admin.milestones.show', $milestone) }}">Cancel</x-button>
                <x-button type="primary" submit>Update Milestone</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
