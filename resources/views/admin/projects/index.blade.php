<x-layouts.admin title="Projects">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Project::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Projects</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage all projects in the system.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\Project::class)
                <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Project
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search projects..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </div>

            <x-select name="company_id" placeholder="All Companies" :options="$companies->pluck('name', 'id')->toArray()" :selected="request('company_id')" />
            <x-select name="client_id" placeholder="All Clients" :options="$clients->pluck('display_name', 'id')->toArray()" :selected="request('client_id')" />
            <x-select name="status" placeholder="All Statuses" :options="['Planning' => 'Planning', 'Pending' => 'Pending', 'In Progress' => 'In Progress', 'On Hold' => 'On Hold', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled', 'Closed' => 'Closed']" :selected="request('status')" />

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200 w-full justify-center sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'priority', 'company_id', 'client_id']))
                    <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors shrink-0">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Code</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Project Name</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Client</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Manager</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden lg:table-cell">Progress</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

        @forelse($projects as $project)
            <tr class="hover:bg-blue-50/30 transition-colors group {{ $project->trashed() ? 'bg-red-50/30' : '' }}">
                <td class="px-6 py-4">
                    <span class="text-sm font-semibold text-gray-700">{{ $project->project_code }}</span>
                </td>
                <td class="px-6 py-4">
                    <div>
                        <a href="{{ route('admin.projects.show', $project) }}" class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                            {{ $project->name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $project->company?->name }} - {{ $project->branch?->name }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 hidden sm:table-cell">
                    <span class="text-sm text-gray-600 font-medium">{{ $project->client?->display_name }}</span>
                </td>
                <td class="px-6 py-4 hidden md:table-cell">
                    <span class="text-sm text-gray-600 font-medium">{{ $project->manager?->name }}</span>
                </td>
                <td class="px-6 py-4">
                    @php
                        $statusType = match($project->status) {
                            'Planning' => 'default',
                            'Pending' => 'warning',
                            'In Progress' => 'primary',
                            'Completed', 'Closed' => 'success',
                            'Cancelled' => 'danger',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ $project->status }}</x-badge>
                    @if($project->trashed())
                        <x-badge type="danger" class="ml-1">Archived</x-badge>
                    @endif
                </td>
                <td class="px-6 py-4 hidden lg:table-cell">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $project->progress }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-600">{{ $project->progress }}%</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    
<div class="flex items-center justify-end gap-2">


                            @can('view', $project)
                                <a href="{{ route('admin.projects.show', $project) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="View">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
</a>
                                <a href="{{ route('admin.projects.timeline', $project) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Timeline">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
</a>
                            @endcan

                            @if(!$project->trashed())
                                @can('update', $project)
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors flex items-center justify-center" title="Edit">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
</a>
                                @endcan

                                @can('create', App\Models\Project::class)
                                    <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Duplicate">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-4M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
</button>
                                    </form>
                                @endcan

                                

                                @if($project->status !== 'Closed')
                                    @can('close', $project)
                                        <form method="POST" action="{{ route('admin.projects.close', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Close">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
</button>
                                        </form>
                                    @endcan
                                @else
                                    @can('reopen', $project)
                                        <form method="POST" action="{{ route('admin.projects.reopen', $project) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Reopen">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
</button>
                                        </form>
                                    @endcan
                                @endif

                                @can('delete', $project)
                                    <button @click="open = false; $dispatch('open-modal', 'archive-project-{{ $project->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Archive">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
</button>
                                @endcan
                            @else
                                @can('restore', $project)
                                    <form method="POST" action="{{ route('admin.projects.restore', $project) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Restore">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
</button>
                                    </form>
                                @endcan
                            @endif
                        
</div>
</td>
            </tr>

            {{-- Archive Modal --}}
            @if(!$project->trashed())
                <x-modal id="archive-project-{{ $project->id }}" maxWidth="md">
                    <x-slot:header>Archive Project</x-slot:header>

                    <div class="text-center py-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive "{{ $project->name }}"?</h3>
                        <p class="text-sm text-gray-500">This action will soft-delete the project. You can restore it later from the deleted items.</p>
                    </div>

                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Archive Project</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endif
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No projects found</h3>
                        <p class="text-sm text-gray-500 font-medium">Create your first project to get started.</p>
                        @can('create', App\Models\Project::class)
                            <a href="{{ route('admin.projects.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                Create Project
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($projects, 'hasPages') && $projects->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $projects->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view projects.</p>
    </div>
    @endcan
</x-layouts.admin>
