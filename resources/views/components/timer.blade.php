@php
    $runningTimer = app(\App\Services\TimerService::class)->getRunningTimer(auth()->id());
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

<div x-data="timerComponent()" x-init="initTimer()" class="relative inline-block">
    <!-- Timer Widget (always visible on navbar/sidebar) -->
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 transition-colors shadow-sm"
         @click="$dispatch('open-modal', 'timer-modal')">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-mono text-sm font-semibold tracking-wider text-gray-700 dark:text-gray-200" x-text="formattedTime">00:00:00</span>
        
        @if($runningTimer)
            <span class="flex h-2 w-2 relative ml-1">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
            </span>
        @endif
    </div>

    <!-- Timer Control Modal -->
    <x-modal id="timer-modal" maxWidth="sm">
        <x-slot:header>Time Tracker</x-slot:header>

        <div class="p-4">
            @if($runningTimer)
                <div class="text-center mb-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Current Session</p>
                    <div class="text-4xl font-mono font-bold text-gray-900 dark:text-white" x-text="formattedTime">00:00:00</div>
                    <p class="mt-2 text-sm text-gray-600 truncate">{{ $runningTimer->project->name }} @if($runningTimer->task) - {{ $runningTimer->task->title }} @endif</p>
                </div>
                
                <div class="flex gap-2 justify-center">
                    <form method="POST" action="{{ route('admin.time-tracking.timer.stop', $runningTimer) }}">
                        @csrf
                        @method('PATCH')
                        <x-button type="danger" submit>
                            <svg class="w-5 h-5 mr-1.5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"></path></svg>
                            Stop Timer
                        </x-button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('admin.time-tracking.timer.start') }}">
                    @csrf
                    <div class="space-y-4">
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
                                    <template x-for="project in filteredProjects" :key="project.id">
                                        <li @click="selectProject(project.id)" class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-blue-50 hover:text-blue-900">
                                            <span class="font-normal block truncate" x-text="project.name"></span>
                                            <span x-show="selectedProjectId == project.id" class="text-blue-600 absolute inset-y-0 right-0 flex items-center pr-4">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        </li>
                                    </template>
                                    <li x-show="filteredProjects.length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9">No projects found.</li>
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
                                    <template x-for="task in filteredTasks" :key="task.id">
                                        <li @click="selectTask(task.id)" class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-blue-50 hover:text-blue-900">
                                            <span class="font-normal block truncate" x-text="task.title"></span>
                                            <span x-show="selectedTaskId == task.id" class="text-blue-600 absolute inset-y-0 right-0 flex items-center pr-4">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        </li>
                                    </template>
                                    <li x-show="filteredTasks.length === 0" class="text-gray-500 cursor-default select-none relative py-2 pl-3 pr-9">No tasks found.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <x-input type="text" name="description" label="What are you working on?" placeholder="e.g., UI Design, Bug fixing..." />
                        
                        <div class="flex items-center">
                            <input type="hidden" name="billable" value="0">
                            <input type="checkbox" name="billable" id="timer_billable" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="timer_billable" class="ml-2 block text-sm text-gray-900">Billable Work</label>
                        </div>
                        
                        <x-button type="primary" class="w-full justify-center" submit>
                            <svg class="w-5 h-5 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Start Timer
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </x-modal>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('timerComponent', () => ({
                projects: @json($mappedProjects),
                selectedProjectId: '',
                selectedTaskId: '',
                projectSearch: '',
                taskSearch: '',
                showProjectDropdown: false,
                showTaskDropdown: false,

                isRunning: {{ $runningTimer ? 'true' : 'false' }},
                startTime: '{{ $runningTimer ? $runningTimer->start_time->toIso8601String() : "" }}',
                accumulatedSeconds: {{ $runningTimer ? ($runningTimer->duration_minutes * 60) : 0 }},
                totalSeconds: 0,
                formattedTime: '00:00:00',
                interval: null,
                
                get filteredProjects() {
                    if (!this.projectSearch) return this.projects;
                    return this.projects.filter(p => p.name.toLowerCase().includes(this.projectSearch.toLowerCase()));
                },
                
                get availableTasks() {
                    if (!this.selectedProjectId) return [];
                    let p = this.projects.find(p => p.id == this.selectedProjectId);
                    return p ? p.tasks : [];
                },
                
                get filteredTasks() {
                    if (!this.taskSearch) return this.availableTasks;
                    return this.availableTasks.filter(t => t.title.toLowerCase().includes(this.taskSearch.toLowerCase()));
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
                },

                initTimer() {
                    if (this.isRunning) {
                        this.updateTime();
                        this.interval = setInterval(() => {
                            this.updateTime();
                        }, 1000);
                    }
                },
                
                updateTime() {
                    let start = new Date(this.startTime);
                    let now = new Date();
                    let diff = Math.floor((now - start) / 1000);
                    this.totalSeconds = diff + this.accumulatedSeconds;
                    
                    let hours = Math.floor(this.totalSeconds / 3600);
                    let minutes = Math.floor((this.totalSeconds % 3600) / 60);
                    let seconds = this.totalSeconds % 60;
                    
                    this.formattedTime = 
                        String(hours).padStart(2, '0') + ':' + 
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');
                }
            }));
        });
    </script>
</div>
