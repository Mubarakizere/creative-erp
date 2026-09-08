<x-layouts.admin title="Projects Management">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Project::class)
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Projects Management</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Track project lifecycles, monitor progress, and manage status transitions.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\Project::class)
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Create New Project
                    </a>
                @endcan
            </div>
        </div>

        {{-- Executive KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Projects</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Across all company branches</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">In Progress</div>
                    <div class="text-2xl font-black text-blue-600 mt-1 flex items-center gap-2">
                        {{ $stats['in_progress'] ?? 0 }}
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">Active operational execution</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Planning / Pending</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['planning_pending'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Awaiting setup or approval</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Completed / Closed</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['completed_closed'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Successfully delivered</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Status Control Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            {{-- Status Filter Quick Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-slate-100">
                @php
                    $currentStatus = request('status');
                    $statuses = [
                        '' => 'All Statuses',
                        'In Progress' => 'In Progress',
                        'Planning' => 'Planning',
                        'Pending' => 'Pending',
                        'On Hold' => 'On Hold',
                        'Completed' => 'Completed',
                        'Closed' => 'Closed',
                        'Cancelled' => 'Cancelled',
                    ];
                @endphp
                @foreach($statuses as $val => $label)
                    <a href="{{ route('admin.projects.index', array_merge(request()->except(['status', 'page']), $val ? ['status' => $val] : [])) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ ($currentStatus === $val) || ($val === '' && !$currentStatus) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search project name, code, contract..." value="{{ request('search') }}" class="block w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                </div>

                <x-select name="company_id" placeholder="All Companies" :options="$companies->pluck('name', 'id')->toArray()" :selected="request('company_id')" class="text-xs" />
                <x-select name="client_id" placeholder="All Clients" :options="$clients->pluck('display_name', 'id')->toArray()" :selected="request('client_id')" class="text-xs" />

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors shadow-xs border border-slate-200 w-full sm:w-auto h-[38px]">
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'status', 'company_id', 'client_id']))
                        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-900 bg-white border border-slate-200 rounded-xl transition-colors shrink-0 h-[38px]">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Projects Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6 w-32">Project Code</th>
                            <th class="py-3.5 px-6">Project Name & Info</th>
                            <th class="py-3.5 px-6 hidden sm:table-cell">Client</th>
                            <th class="py-3.5 px-6 hidden md:table-cell">Project Manager</th>
                            <th class="py-3.5 px-6 text-center">Status & Quick Action</th>
                            <th class="py-3.5 px-6 hidden lg:table-cell">Progress</th>
                            <th class="py-3.5 px-6 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($projects as $project)
                            <tr class="hover:bg-slate-50/80 transition-colors group {{ $project->trashed() ? 'bg-rose-50/40' : '' }}">
                                <td class="py-4 px-6 font-mono font-extrabold text-slate-900">
                                    <span class="inline-block bg-slate-100 border border-slate-200 text-slate-800 px-2 py-1 rounded-lg text-[11px]">
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div>
                                        <a href="{{ route('admin.projects.show', $project) }}" class="text-sm font-extrabold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $project->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-2">
                                            <span>{{ $project->company?->name }}</span>
                                            @if($project->branch)
                                                <span>•</span>
                                                <span>{{ $project->branch->name }}</span>
                                            @endif
                                            @if($project->contract_number)
                                                <span>•</span>
                                                <span class="font-mono text-slate-400">#{{ $project->contract_number }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-700 hidden sm:table-cell">
                                    {{ $project->client?->display_name ?? '—' }}
                                </td>
                                <td class="py-4 px-6 hidden md:table-cell">
                                    <div class="font-bold text-slate-900">{{ $project->manager?->full_name ?? $project->manager?->name ?? 'Unassigned' }}</div>
                                    @php
                                        $pmMember = $project->projectMembers?->firstWhere('user_id', $project->project_manager_id);
                                    @endphp
                                    @if($pmMember && $pmMember->project_role)
                                        <div class="text-[10px] text-indigo-600 font-semibold">{{ $pmMember->project_role }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5" x-data="{ openStatus: false }">
                                        {{-- Custom Interactive Status Badge --}}
                                        @switch($project->status)
                                            @case('In Progress')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                                                    </span>
                                                    In Progress
                                                </span>
                                                @break
                                            @case('Completed')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Completed
                                                </span>
                                                @break
                                            @case('Closed')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    Closed
                                                </span>
                                                @break
                                            @case('On Hold')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    On Hold
                                                </span>
                                                @break
                                            @case('Pending')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Pending
                                                </span>
                                                @break
                                            @case('Cancelled')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Cancelled
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    Planning
                                                </span>
                                        @endswitch

                                        @if($project->trashed())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800">Archived</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 hidden lg:table-cell">
                                    <div class="flex items-center gap-2.5 min-w-[130px]">
                                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-900 w-8 text-right">{{ $project->progress }}%</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <x-action-dropdown>
                                        @can('view', $project)
                                            <x-action-dropdown-item href="{{ route('admin.projects.show', $project) }}" icon="view">
                                                View Project Details
                                            </x-action-dropdown-item>
                                            <x-action-dropdown-item href="{{ route('admin.projects.timeline', $project) }}">
                                                <x-slot:icon>
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </x-slot:icon>
                                                Timeline Chart
                                            </x-action-dropdown-item>
                                        @endcan

                                        @if(!$project->trashed())
                                            @can('update', $project)
                                                <x-action-dropdown-item href="{{ route('admin.projects.edit', $project) }}" icon="edit">
                                                    Edit Project
                                                </x-action-dropdown-item>

                                                {{-- Quick Status Change Options --}}
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Change Status</div>

                                                @if($project->status !== 'In Progress')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-in-progress-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="In Progress">
                                                    </form>
                                                    <x-action-dropdown-item onclick="document.getElementById('status-in-progress-form-{{ $project->id }}').submit()">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-blue-500"></span></x-slot:icon>
                                                        Mark In Progress
                                                    </x-action-dropdown-item>
                                                @endif

                                                @if($project->status !== 'On Hold')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-on-hold-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="On Hold">
                                                    </form>
                                                    <x-action-dropdown-item onclick="document.getElementById('status-on-hold-form-{{ $project->id }}').submit()">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-amber-500"></span></x-slot:icon>
                                                        Put On Hold
                                                    </x-action-dropdown-item>
                                                @endif

                                                @if($project->status !== 'Completed')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-completed-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Completed">
                                                    </form>
                                                    <x-action-dropdown-item onclick="document.getElementById('status-completed-form-{{ $project->id }}').submit()">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-emerald-500"></span></x-slot:icon>
                                                        Mark Completed
                                                    </x-action-dropdown-item>
                                                @endif

                                                @if($project->status !== 'Closed')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-closed-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Closed">
                                                    </form>
                                                    <x-action-dropdown-item onclick="document.getElementById('status-closed-form-{{ $project->id }}').submit()">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-slate-600"></span></x-slot:icon>
                                                        Close Project
                                                    </x-action-dropdown-item>
                                                @endif

                                                @if($project->status !== 'Planning')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-planning-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Planning">
                                                    </form>
                                                    <x-action-dropdown-item onclick="document.getElementById('status-planning-form-{{ $project->id }}').submit()">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-slate-400"></span></x-slot:icon>
                                                        Set to Planning
                                                    </x-action-dropdown-item>
                                                @endif

                                                @if($project->status !== 'Cancelled')
                                                    <form method="POST" action="{{ route('admin.projects.status.update', $project) }}" id="status-cancelled-form-{{ $project->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="Cancelled">
                                                    </form>
                                                    <x-action-dropdown-item onclick="if(confirm('Are you sure you want to cancel this project?')) document.getElementById('status-cancelled-form-{{ $project->id }}').submit()" variant="danger">
                                                        <x-slot:icon><span class="w-2 h-2 rounded-full bg-rose-500"></span></x-slot:icon>
                                                        Mark Cancelled
                                                    </x-action-dropdown-item>
                                                @endif
                                            @can('create', App\Models\Project::class)
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}" id="duplicate-project-form-{{ $project->id }}">
                                                    @csrf
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('duplicate-project-form-{{ $project->id }}').submit()">
                                                    <x-slot:icon>
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-4M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                                    </x-slot:icon>
                                                    Duplicate Project
                                                </x-action-dropdown-item>
                                            @endcan
                                            @endcan

                                            @can('delete', $project)
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" id="delete-project-form-{{ $project->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this project?')) document.getElementById('delete-project-form-{{ $project->id }}').submit()" icon="delete" variant="danger">
                                                    Delete Project
                                                </x-action-dropdown-item>
                                            @endcan
                                        @else
                                            @can('restore', $project)
                                                <form method="POST" action="{{ route('admin.projects.restore', $project) }}" id="restore-project-form-{{ $project->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                <x-action-dropdown-item onclick="document.getElementById('restore-project-form-{{ $project->id }}').submit()">
                                                    <x-slot:icon>
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    </x-slot:icon>
                                                    Restore Project
                                                </x-action-dropdown-item>
                                            @endcan
                                        @endif
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 px-6 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">No projects found</h3>
                                    <p class="text-slate-500 text-xs max-w-sm mx-auto mb-5">There are no projects matching your search criteria or selected status filter.</p>
                                    @can('create', App\Models\Project::class)
                                        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                                            Create New Project
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($projects, 'hasPages') && $projects->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $projects->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
        <p class="text-sm text-slate-500 font-medium">You do not have permission to view projects.</p>
    </div>
    @endcan
</x-layouts.admin>
