<x-layouts.admin title="Project Teams">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Project Teams'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Project Teams</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Organize and manage team member assignments per project.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('project-team.assign')
                <a href="{{ route('admin.projects.team.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Assign Member
                </a>
            @endcan
        </div>
    </div>

    {{-- Filter Bar & View Toggle --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-200/70 shadow-xs mb-6 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Tabs --}}
            <div class="inline-flex p-1 bg-gray-100/80 rounded-xl border border-gray-200/60">
                <a href="{{ route('admin.projects.team.index', array_merge(request()->except('view'), ['view' => 'projects'])) }}"
                   @class([
                       'px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 flex items-center gap-2',
                       'bg-white text-gray-900 shadow-xs' => request('view', 'projects') === 'projects',
                       'text-gray-500 hover:text-gray-900' => request('view', 'projects') !== 'projects',
                   ])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Grouped by Project
                </a>
                <a href="{{ route('admin.projects.team.index', array_merge(request()->except('view'), ['view' => 'members'])) }}"
                   @class([
                       'px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 flex items-center gap-2',
                       'bg-white text-gray-900 shadow-xs' => request('view') === 'members',
                       'text-gray-500 hover:text-gray-900' => request('view') !== 'members',
                   ])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    All Members List
                </a>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('admin.projects.team.index') }}" method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="hidden" name="view" value="{{ request('view', 'projects') }}">

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search project or member..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50/50 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>
                
                <x-select name="project_id" placeholder="All Projects" :options="$projects->pluck('name', 'id')->toArray()" :selected="request('project_id')" />
                
                <div class="flex items-center gap-2">
                    <div class="flex-1">
                        <x-select name="department_id" placeholder="All Departments" :options="$departments->pluck('name', 'id')->toArray()" :selected="request('department_id')" />
                    </div>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors shrink-0 h-[42px]">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(request('view', 'projects') === 'projects')
        {{-- Grouped by Project Cards View --}}
        <div class="space-y-6">
            @forelse($projectTeams as $project)
                <div class="bg-white rounded-2xl border border-gray-200/70 shadow-xs overflow-hidden transition-all duration-200 hover:shadow-sm" x-data="{ expanded: true }">
                    {{-- Project Header --}}
                    <div class="p-5 bg-gray-50/40 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.projects.show', $project) }}" class="text-base font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                        {{ $project->name }}
                                    </a>
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-mono font-medium">{{ $project->project_code }}</span>
                                    <x-badge :type="match($project->status) { 'In Progress' => 'success', 'Planning' => 'info', 'Completed' => 'default', default => 'warning' }">
                                        {{ $project->status }}
                                    </x-badge>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span>Manager: <strong class="text-gray-700">{{ $project->manager->name ?? 'Unassigned' }}</strong></span>
                                    <span>•</span>
                                    <span>Team Size: <strong class="text-gray-700">{{ $project->projectMembers->count() }} members</strong></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 self-end sm:self-auto">
                            @can('project-team.assign')
                                <a href="{{ route('admin.projects.team.create', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Member
                                </a>
                            @endcan
                            <button @click="expanded = !expanded" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Members Grid / List --}}
                    <div x-show="expanded" x-collapse>
                        @if($project->projectMembers->count() > 0)
                            <div class="divide-y divide-gray-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
                                    @foreach($project->projectMembers as $member)
                                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-gray-200 hover:shadow-xs transition-all duration-200 flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center shrink-0">
                                                    {{ $member->user->initials ?? substr($member->user->name ?? 'U', 0, 2) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $member->user->full_name ?? $member->user->name }}</h4>
                                                    <p class="text-xs font-semibold text-blue-600 mt-0.5">{{ $member->project_role }}</p>
                                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                                        <span>{{ $member->department->name ?? 'General' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $member->allocation_percentage }}% workload</span>
                                                    </p>
                                                </div>
                                            </div>

                                            <x-action-dropdown>
                                                @can('view', $member)
                                                    <x-action-dropdown-item :href="route('admin.projects.team.show', $member)">
                                                        View Details
                                                    </x-action-dropdown-item>
                                                @endcan
                                                @can('update', $member)
                                                    <x-action-dropdown-item :href="route('admin.projects.team.edit', $member)">
                                                        Edit Role
                                                    </x-action-dropdown-item>
                                                @endcan
                                                @can('delete', $member)
                                                    <form method="POST" action="{{ route('admin.projects.team.destroy', $member) }}" class="block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                                                            Remove Member
                                                        </button>
                                                    </form>
                                                @endcan
                                            </x-action-dropdown>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center text-gray-400 text-sm">
                                No team members assigned to this project yet.
                                @can('project-team.assign')
                                    <a href="{{ route('admin.projects.team.create', ['project_id' => $project->id]) }}" class="text-blue-600 font-semibold hover:underline ml-1">
                                        Assign first member
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-2xl border border-gray-200/70 text-gray-500">
                    No projects found matching criteria.
                </div>
            @endforelse

            <div class="mt-4">
                {{ $projectTeams->links('components.pagination') }}
            </div>
        </div>
    @else
        {{-- All Members Table View --}}
        <x-card :padding="false">
            <x-table>
                <x-slot:head>
                    <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Member</th>
                    <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Project</th>
                    <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Role & Dept</th>
                    <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Allocation</th>
                    <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                </x-slot:head>

                @forelse($members as $member)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $member->user->initials ?? substr($member->user->name ?? 'U', 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $member->user->full_name ?? $member->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.projects.show', $member->project_id) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                {{ $member->project->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $member->project_role }}</div>
                            <div class="text-xs text-gray-500">{{ $member->department->name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ $member->allocation_percentage }}%
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="$member->status === 'Active' ? 'success' : 'default'">
                                {{ $member->status }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-action-dropdown>
                                @can('view', $member)
                                    <x-action-dropdown-item :href="route('admin.projects.team.show', $member)">
                                        View Details
                                    </x-action-dropdown-item>
                                @endcan
                                @can('update', $member)
                                    <x-action-dropdown-item :href="route('admin.projects.team.edit', $member)">
                                        Edit Assignment
                                    </x-action-dropdown-item>
                                @endcan
                                @can('delete', $member)
                                    <form method="POST" action="{{ route('admin.projects.team.destroy', $member) }}" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                                            Remove Member
                                        </button>
                                    </form>
                                @endcan
                            </x-action-dropdown>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No team members found.
                        </td>
                    </tr>
                @endforelse

                <x-slot:pagination>
                    {{ $members->links('components.pagination') }}
                </x-slot:pagination>
            </x-table>
        </x-card>
    @endif
</x-layouts.admin>
