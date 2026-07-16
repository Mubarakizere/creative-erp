<x-layouts.admin title="Milestone Details">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Milestones', 'url' => route('admin.milestones.index')],
                ['label' => $milestone->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $milestone->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Project: <a href="{{ route('admin.projects.show', $milestone->project) }}" class="text-blue-600 hover:underline">{{ $milestone->project->name }}</a></p>
            </div>
            <div class="flex gap-2">
                @can('view', $milestone)
                    <x-button type="ghost" href="{{ route('admin.milestones.timeline', $milestone) }}" size="sm">
                        Timeline
                    </x-button>
                @endcan
                
                @if(!$milestone->trashed())
                    @can('update', $milestone)
                        <x-button type="primary" href="{{ route('admin.milestones.edit', $milestone) }}" size="sm">
                            Edit
                        </x-button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div x-data="{ activeTab: 'overview' }" class="mb-8">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'overview'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Overview
                </button>
                <button @click="activeTab = 'tasks'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tasks' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Tasks
                </button>
                <button @click="activeTab = 'documents'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'documents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'documents' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Documents
                </button>
                <button @click="activeTab = 'activity'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'activity', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'activity' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Activity
                </button>
                <button @click="activeTab = 'discussions'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'discussions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'discussions' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                    Discussions
                    @if($milestone->comments()->count() > 0)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">{{ $milestone->comments()->count() }}</span>
                    @endif
                </button>
            </nav>
        </div>
        
        <div class="mt-6">
            {{-- Overview Tab --}}
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <x-card>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Milestone Information</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Priority</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <x-badge :type="match($milestone->priority) { 'Critical' => 'danger', 'High' => 'warning', 'Low' => 'default', default => 'primary' }">
                                            {{ $milestone->priority }}
                                        </x-badge>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Status</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <x-badge :type="match($milestone->status) { 'Completed' => 'success', 'On Hold' => 'danger', 'In Progress' => 'primary', 'Pending' => 'warning', default => 'default' }">
                                            {{ $milestone->status }}
                                        </x-badge>
                                    </p>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Progress</span>
                                    <div class="mt-1 flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden max-w-[150px]">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $milestone->progress }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $milestone->progress }}%</span>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Description</span>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $milestone->description ?: 'No description provided.' }}</p>
                                </div>
                            </div>
                        </x-card>
                    </div>
                    
                    <div class="space-y-6">
                        <x-card>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Dates</h3>
                            <ul class="space-y-4">
                                <li>
                                    <span class="block text-xs font-semibold text-gray-500 uppercase">Start Date</span>
                                    <span class="block text-sm text-gray-900">{{ $milestone->start_date?->format('F j, Y') ?? 'N/A' }}</span>
                                </li>
                                <li>
                                    <span class="block text-xs font-semibold text-gray-500 uppercase">Due Date</span>
                                    <span class="block text-sm text-gray-900">{{ $milestone->due_date?->format('F j, Y') ?? 'N/A' }}</span>
                                </li>
                                <li>
                                    <span class="block text-xs font-semibold text-gray-500 uppercase">Completed Date</span>
                                    <span class="block text-sm text-gray-900">{{ $milestone->completed_at?->format('F j, Y') ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </x-card>
                    </div>
                </div>
            </div>
            
            {{-- Tasks Tab --}}
            <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Milestone Tasks</h3>
                    @can('assignTasks', $milestone)
                        <button x-data @click="$dispatch('open-modal', 'assign-tasks')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Assign Tasks
                        </button>
                    @endcan
                </div>

                <x-table>
                    <x-slot:head>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Task</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
                    </x-slot:head>

                    @forelse($milestone->tasks as $task)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-gray-700">{{ $task->task_code }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-900">{{ $task->title }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600">{{ $task->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600">{{ $task->priority }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('assignTasks', $milestone)
                                    <form method="POST" action="{{ route('admin.milestones.remove-task', [$milestone, $task->id]) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Remove Task">
                                            Remove
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No tasks assigned to this milestone.</td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
            
            {{-- Documents Tab --}}
            <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                @include('admin.documents.partials.document_tab', ['documentable' => $milestone])
            </div>
            
            {{-- Activity Tab --}}
            <div x-show="activeTab === 'activity'" style="display: none;" x-cloak>
                <x-card>
                    <div class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900">Activity Log</h3>
                        <p class="mt-1 text-sm text-gray-500">This feature will be available in the upcoming sprints.</p>
                    </div>
                </x-card>
            </div>

            <div x-show="activeTab === 'discussions'" style="display: none;" x-cloak>
                <x-discussions :model="$milestone" />
            </div>
        </div>
    </div>

    @can('assignTasks', $milestone)
        <x-modal id="assign-tasks" maxWidth="lg">
            <x-slot:header>Assign Tasks</x-slot:header>
            
            <form method="POST" action="{{ route('admin.milestones.assign-tasks', $milestone) }}">
                @csrf
                <div class="py-4">
                    <p class="text-sm text-gray-500 mb-4">Select project tasks to assign to this milestone.</p>
                    <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-md">
                        @forelse($projectTasks as $task)
                            <label class="flex items-center px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer">
                                <input type="checkbox" name="task_ids[]" value="{{ $task->id }}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-3 flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $task->task_code }}: {{ $task->title }}</span>
                                    <span class="text-xs text-gray-500">Status: {{ $task->status }}</span>
                                </span>
                            </label>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">
                                No unassigned tasks available in this project.
                            </div>
                        @endforelse
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="ghost" @click="$dispatch('close-modal', 'assign-tasks')">Cancel</x-button>
                    <x-button type="primary" submit>Assign Selected Tasks</x-button>
                </x-slot:footer>
            </form>
        </x-modal>
    @endcan
</x-layouts.admin>
