<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Project Tasks</h3>
            <p class="mt-1 text-sm text-gray-500">Manage tasks and track progress for this project.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\Task::class)
                <x-button type="primary" href="{{ route('admin.projects.tasks.create', ['project_id' => $project->id]) }}" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Task
                </x-button>
            @endcan
            <x-button type="ghost" href="{{ route('admin.projects.tasks.index', ['project_id' => $project->id]) }}" size="sm">
                View All
            </x-button>
        </div>
    </div>

    {{-- Task Summary Stats --}}
    @php
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'Completed')->count();
        $openTasks = $totalTasks - $completedTasks;
        $overdueTasks = $project->tasks()->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->count();
        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card class="bg-gray-50 border-none shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-gray-900">{{ $totalTasks }}</span>
                    <span class="block text-xs font-medium text-gray-500 uppercase">Total Tasks</span>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gray-50 border-none shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-gray-900">{{ $openTasks }}</span>
                    <span class="block text-xs font-medium text-gray-500 uppercase">Open Tasks</span>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gray-50 border-none shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-gray-900">{{ $completedTasks }}</span>
                    <span class="block text-xs font-medium text-gray-500 uppercase">Completed</span>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gray-50 border-none shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-gray-900">{{ $overdueTasks }}</span>
                    <span class="block text-xs font-medium text-gray-500 uppercase">Overdue</span>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Progress Bar --}}
    <x-card>
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Task Completion</span>
            <span class="text-sm font-bold text-gray-900">{{ $taskProgress }}%</span>
        </div>
        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $taskProgress }}%"></div>
        </div>
    </x-card>

    {{-- Recent Tasks --}}
    <x-card>
        <h4 class="text-md font-medium text-gray-900 mb-4 border-b pb-2">Recent Tasks</h4>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($project->tasks()->orderBy('updated_at', 'desc')->take(5)->get() as $task)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.projects.tasks.show', $task) }}'">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->task_code }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $task->name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $task->assignee?->full_name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusType = match($task->status) {
                                        'Pending' => 'default',
                                        'In Progress' => 'primary',
                                        'Waiting Review' => 'warning',
                                        'Completed' => 'success',
                                        'Cancelled' => 'danger',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ $task->status }}</x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden w-16">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ $task->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $task->progress }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                No tasks created for this project yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
