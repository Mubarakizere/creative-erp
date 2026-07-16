@php
    // Fetch user's assigned projects with their tasks
    $userProjects = auth()->user()->projects()->with('tasks')->get();
    $mappedProjects = $userProjects->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'tasks' => $p->tasks->map(function($t) {
                return ['id' => $t->id, 'title' => $t->title];
            })->values()->toArray()
        ];
    })->values()->toArray();
@endphp

<div x-data="projectTaskSearchComponent()" class="space-y-4">
    <!-- Searchable Project Dropdown -->
    <div class="relative space-y-1.5" @click.outside="showProjectDropdown = false">
        <label class="block text-sm font-medium text-gray-700">Project <span class="text-red-500">*</span></label>
        <input type="hidden" name="project_id" :value="selectedProjectId" required>
        
        <button type="button" @click="showProjectDropdown = !showProjectDropdown" class="w-full bg-white border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 text-left sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            <span x-text="selectedProjectName"></span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" style="top: 28px;">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
            </span>
        </button>
        
        <div x-show="showProjectDropdown" x-cloak class="absolute z-10 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm mt-1">
            <div class="px-2 py-2 sticky top-0 bg-white">
                <input type="text" x-model="projectSearch" class="w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Search project...">
            </div>
            <ul class="max-h-48 overflow-y-auto">
                <template x-for="project in projects.filter(p => p.name.toLowerCase().includes(projectSearch.toLowerCase()))" :key="project.id">
                    <li @click="selectProject(project.id)" class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-blue-50 hover:text-blue-900">
                        <span class="font-normal block truncate" x-text="project.name"></span>
                        <span x-show="selectedProjectId == project.id" class="text-blue-600 absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                    </li>
                </template>
                <li x-show="projects.filter(p => p.name.toLowerCase().includes(projectSearch.toLowerCase())).length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9">No projects found.</li>
            </ul>
        </div>
    </div>

    <!-- Searchable Task Dropdown -->
    <div class="relative space-y-1.5" @click.outside="showTaskDropdown = false">
        <label class="block text-sm font-medium text-gray-700">Task (Optional)</label>
        <input type="hidden" name="task_id" :value="selectedTaskId">
        
        <button type="button" @click="showTaskDropdown = !showTaskDropdown" :disabled="!selectedProjectId" class="w-full bg-white border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 text-left sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500">
            <span x-text="selectedTaskName"></span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" style="top: 28px;">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
            </span>
        </button>
        
        <div x-show="showTaskDropdown" x-cloak class="absolute z-10 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm mt-1">
            <div class="px-2 py-2 sticky top-0 bg-white">
                <input type="text" x-model="taskSearch" class="w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Search task...">
            </div>
            <ul class="max-h-48 overflow-y-auto">
                <template x-for="task in availableTasks.filter(t => t.title.toLowerCase().includes(taskSearch.toLowerCase()))" :key="task.id">
                    <li @click="selectTask(task.id)" class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-blue-50 hover:text-blue-900">
                        <span class="font-normal block truncate" x-text="task.title"></span>
                        <span x-show="selectedTaskId == task.id" class="text-blue-600 absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </span>
                    </li>
                </template>
                <li x-show="availableTasks.filter(t => t.title.toLowerCase().includes(taskSearch.toLowerCase())).length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9">No tasks found.</li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // Register it only if it doesn't already exist to prevent duplicate definitions
        if (!Alpine.data('projectTaskSearchComponent')) {
            Alpine.data('projectTaskSearchComponent', () => ({
                projects: @json($mappedProjects),
                selectedProjectId: '',
                selectedTaskId: '',
                projectSearch: '',
                taskSearch: '',
                showProjectDropdown: false,
                showTaskDropdown: false,
                
                get availableTasks() {
                    if (!this.selectedProjectId) return [];
                    let p = this.projects.find(p => p.id == this.selectedProjectId);
                    return p ? p.tasks : [];
                },

                selectProject(id) {
                    this.selectedProjectId = id;
                    this.selectedTaskId = '';
                    this.showProjectDropdown = false;
                    this.projectSearch = '';
                },

                selectTask(id) {
                    this.selectedTaskId = id;
                    this.showTaskDropdown = false;
                    this.taskSearch = '';
                },
                
                get selectedProjectName() {
                    let p = this.projects.find(p => p.id == this.selectedProjectId);
                    return p ? p.name : 'Select Project';
                },

                get selectedTaskName() {
                    let t = this.availableTasks.find(t => t.id == this.selectedTaskId);
                    return t ? t.title : 'Select Task (Optional)';
                }
            }));
        }
    });
</script>
