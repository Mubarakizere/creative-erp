<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ 
    projectId: '{{ old('project_id', $task->project_id ?? request('project_id') ?? '') }}',
    status: '{{ old('status', $task->status ?? 'Pending') }}',
    progress: {{ old('progress', $task->progress ?? 0) }}
}">
    <div class="lg:col-span-2 space-y-6">
        {{-- Basic Information --}}
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900">Task Information</h3>
                <p class="mt-1 text-sm text-gray-500">Provide the basic details for this task.</p>
            </x-slot:header>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Project --}}
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                        @if($task)
                            <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                            <x-input disabled value="{{ $task->project->name }}" class="bg-gray-50" name="project_name_disabled" />
                        @else
                            <select name="project_id" id="project_id" x-model="projectId" @change="window.location.href = '?project_id=' + projectId"
                                class="block w-full rounded-lg border {{ $errors->has('project_id') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm text-sm py-2.5 pl-3 pr-10 transition-colors duration-200">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Task Code --}}
                    <div>
                        <x-input label="Task Code" name="task_code" type="text" :value="old('task_code', $task->task_code ?? '')" placeholder="e.g. TSK-001" required="true" />
                    </div>
                </div>

                {{-- Task Name --}}
                <div>
                    <x-input label="Task Name" name="name" type="text" :value="old('name', $task->name ?? '')" placeholder="Enter task name" required="true" />
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" 
                        class="block w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 text-red-900' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500' }} shadow-sm sm:text-sm" 
                        placeholder="Detailed description of the task...">{{ old('description', $task->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                {{-- Parent Task (Placeholder, typically fetched dynamically) --}}
                @if($task || request('project_id'))
                    <div>
                        <x-select label="Parent Task (Optional)" name="parent_id" :options="App\Models\Task::where('project_id', $task->project_id ?? request('project_id'))->where('id', '!=', $task->id ?? 0)->pluck('name', 'id')->toArray()" :selected="old('parent_id', $task->parent_id ?? '')" placeholder="None" />
                    </div>
                @endif
            </div>
        </x-card>
    </div>

    <div class="space-y-6">
        {{-- Assignment & Status --}}
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900">Assignment & Status</h3>
            </x-slot:header>

            <div class="space-y-4">
                {{-- Assignee --}}
                <div>
                    @php
                        $assignees = [];
                        if ($task) {
                            $members = $task->project->projectMembers()->where('status', 'Active')->with('user')->get();
                            foreach($members as $member) {
                                $assignees[$member->user_id] = $member->user->full_name;
                            }
                        } elseif (isset($selectedProject)) {
                            $members = $selectedProject->projectMembers()->where('status', 'Active')->with('user')->get();
                            foreach($members as $member) {
                                $assignees[$member->user_id] = $member->user->full_name;
                            }
                        }
                    @endphp
                    @if($task || isset($selectedProject))
                        <x-select label="Assign To" name="assigned_to" :options="$assignees" :selected="old('assigned_to', $task->assigned_to ?? '')" placeholder="Unassigned" />
                    @else
                        <label class="block text-sm font-medium text-gray-700">Assign To</label>
                        <p class="text-sm text-gray-500 italic mt-1">Select a project first to assign members.</p>
                    @endif
                </div>

                {{-- Priority --}}
                <div>
                    <x-select label="Priority" name="priority" :options="['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical']" :selected="old('priority', $task->priority ?? 'Medium')" required="true" placeholder="" />
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" x-model="status" @change="if(status === 'Completed') progress = 100;"
                        class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm py-2.5 pl-3 pr-10 transition-colors duration-200">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Waiting Review">Waiting Review</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Progress --}}
                <div>
                    <div class="flex justify-between">
                        <label for="progress" class="block text-sm font-medium text-gray-700 mb-1">Progress (%) <span class="text-red-500">*</span></label>
                        <span class="text-sm font-medium text-gray-700" x-text="progress + '%'"></span>
                    </div>
                    <input type="range" name="progress" id="progress" min="0" max="100" x-model="progress" :disabled="status === 'Completed'"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2">
                    @error('progress')
                        <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1" x-show="status === 'Completed'">Progress is locked at 100% when completed.</p>
                </div>
            </div>
        </x-card>

        {{-- Dates --}}
        <x-card>
            <x-slot:header>
                <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
            </x-slot:header>

            <div class="space-y-4">
                {{-- Start Date --}}
                <div>
                    <x-input label="Start Date" id="start_date" name="start_date" type="date" :value="old('start_date', optional($task->start_date ?? null)->format('Y-m-d') ?? '')" required="true" />
                </div>

                {{-- Due Date --}}
                <div>
                    <x-input label="Due Date" id="due_date" name="due_date" type="date" :value="old('due_date', optional($task->due_date ?? null)->format('Y-m-d') ?? '')" />
                </div>
            </div>
        </x-card>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-4">
            <x-button type="ghost" href="{{ $task ? route('admin.projects.tasks.show', $task) : route('admin.projects.tasks.index') }}">
                Cancel
            </x-button>
            <x-button type="primary" submit="true">
                {{ $task ? 'Update Task' : 'Create Task' }}
            </x-button>
        </div>
    </div>
</div>
