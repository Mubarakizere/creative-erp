<x-layouts.admin title="Task: {{ $task->task_code }}">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $task->project->name, 'url' => route('admin.projects.show', $task->project)],
                ['label' => 'Tasks', 'url' => route('admin.projects.tasks.index', ['project_id' => $task->project_id])],
                ['label' => $task->task_code],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $task->name }}</h1>
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
                <x-badge :type="$statusType" size="lg">{{ $task->status }}</x-badge>
            </div>
            <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    {{ $task->task_code }}
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ $task->project->name }}
                </span>
                @if($task->trashed())
                    <x-badge type="danger">Archived</x-badge>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @if(!$task->trashed())
                @can('update', $task)
                    <x-button type="ghost" href="{{ route('admin.projects.tasks.edit', $task) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Task
                    </x-button>
                @endcan
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                </x-slot:header>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($task->description ?? 'No description provided.')) !!}
                </div>
            </x-card>

            {{-- Subtasks placeholder --}}
            <x-card>
                <x-slot:header>
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Subtasks</h3>
                        @can('create', App\Models\Task::class)
                            <x-button type="ghost" size="sm" href="{{ route('admin.projects.tasks.create', ['project_id' => $task->project_id, 'parent_id' => $task->id]) }}">
                                Add Subtask
                            </x-button>
                        @endcan
                    </div>
                </x-slot:header>
                
                @if($task->children->count() > 0)
                    <div class="space-y-3">
                        @foreach($task->children as $child)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($child->status === 'Completed')
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.projects.tasks.show', $child) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $child->name }}</a>
                                        <p class="text-xs text-gray-500">{{ $child->task_code }} &bull; {{ $child->assignee?->full_name ?? 'Unassigned' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $child->progress }}%</span>
                                    @php
                                        $childType = match($child->status) {
                                            'Pending' => 'default',
                                            'In Progress' => 'primary',
                                            'Waiting Review' => 'warning',
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger',
                                            default => 'default',
                                        };
                                    @endphp
                                    <x-badge :type="$childType">{{ $child->status }}</x-badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-sm">No subtasks found.</p>
                    </div>
                @endif
            </x-card>

            {{-- Documents --}}
            <div class="mt-6">
                @include('admin.documents.partials.document_tab', ['documentable' => $task])
            </div>

            <div class="mt-6">
                <x-discussions :model="$task" />
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Task Details --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-medium text-gray-900">Details</h3>
                </x-slot:header>

                <div class="space-y-4">
                    {{-- Assignee --}}
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Assignee</span>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm">
                                {{ substr($task->assignee?->full_name ?? 'U', 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $task->assignee?->full_name ?? 'Unassigned' }}</span>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Priority</span>
                        @php
                            $priorityType = match($task->priority) {
                                'Low' => 'default',
                                'Medium' => 'primary',
                                'High' => 'warning',
                                'Critical' => 'danger',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$priorityType">{{ $task->priority }}</x-badge>
                    </div>

                    {{-- Progress --}}
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Progress</span>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full" style="width: {{ $task->progress }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $task->progress }}%</span>
                        </div>
                    </div>

                    {{-- Total Tracked Time --}}
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Tracked Time</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-900">
                                @php
                                    $taskMinutes = $task->timeEntries()->where('status', 'completed')->sum('duration_minutes');
                                @endphp
                                {{ intdiv($taskMinutes, 60) }}h {{ $taskMinutes % 60 }}m
                            </span>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Start Date</span>
                            <span class="text-sm text-gray-900">{{ $task->start_date->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Due Date</span>
                            <span class="text-sm text-gray-900">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'Not Set' }}</span>
                        </div>
                    </div>

                    @if($task->completed_at)
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Completed At</span>
                            <span class="text-sm text-gray-900">{{ $task->completed_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- Related Info --}}
            <x-card>
                <div class="space-y-3 text-sm text-gray-500">
                    <div class="flex justify-between">
                        <span>Created By:</span>
                        <span class="font-medium text-gray-900">{{ $task->creator->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Created At:</span>
                        <span class="font-medium text-gray-900">{{ $task->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Last Updated:</span>
                        <span class="font-medium text-gray-900">{{ $task->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
