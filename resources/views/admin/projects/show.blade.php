<x-layouts.admin title="Project Profile">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $project->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Premium Hero Header --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 mb-6 overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 text-xs font-mono font-bold text-blue-700 bg-blue-50 border border-blue-100 rounded-lg">{{ $project->project_code }}</span>
                    
                    @php
                        $statusType = match($project->status) {
                            'Completed', 'Closed' => 'success',
                            'Cancelled' => 'danger',
                            'In Progress' => 'primary',
                            'Pending' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ $project->status }}</x-badge>
                    
                    @php
                        $userProjectRole = $project->projectMembers?->firstWhere('user_id', auth()->id())?->project_role;
                    @endphp
                    @if($userProjectRole)
                        <span class="px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-lg">
                            Your Role: {{ $userProjectRole }}
                        </span>
                    @endif

                    @if($project->priority === 'Critical')
                        <span class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg animate-pulse flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Critical Priority
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 truncate">
                    {{ $project->name }}
                </h1>

                <div class="flex flex-wrap items-center text-xs font-medium text-slate-500 gap-4 mt-3">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ $project->company?->name ?? 'N/A' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $project->client?->display_name ?? 'No Client Assigned' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start lg:self-center">
                @can('view', $project)
                    <a href="{{ route('admin.projects.timeline', $project) }}" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-xs">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Timeline Chart
                    </a>
                @endcan
                
                @if(!$project->trashed())
                    @can('update', $project)
                        <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-5 py-2.5 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-xs hover:shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Project
                        </a>
                    @endcan
                @endif
            </div>
        </div>
        
        {{-- Progress Bar & Timeline Dates --}}
        <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1 w-full max-w-xl">
                <div class="flex justify-between text-xs font-semibold text-slate-700 mb-1.5">
                    <span class="uppercase tracking-wider">Overall Project Progress</span>
                    <span class="text-blue-700 font-bold">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-700" style="width: {{ $project->progress }}%"></div>
                </div>
            </div>
            
            <div class="flex items-center gap-6 text-xs">
                <div>
                    <span class="block text-slate-400 font-medium uppercase tracking-wider">Start Date</span>
                    <span class="block font-bold text-slate-900 mt-0.5">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <div>
                    <span class="block text-slate-400 font-medium uppercase tracking-wider">Due Date</span>
                    <span class="block font-bold mt-0.5 {{ $project->planned_end_date && $project->planned_end_date->isPast() && $project->status !== 'Completed' ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $project->planned_end_date?->format('M d, Y') ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Executive Health KPI Summary Cards --}}
    @php
        $estBudget = $project->estimated_budget ?? 0;
        $actBudget = $project->actual_budget ?? 0;
        $estCost = $project->estimated_cost ?? 0;
        $actCost = $project->actual_cost ?? 0;
        $totalTasksCount = $project->tasks()->count();
        $completedTasksCount = $project->tasks()->where('status', 'Completed')->count();
        $taskPct = $totalTasksCount > 0 ? round(($completedTasksCount / $totalTasksCount) * 100) : 0;
        $totalMinutes = $project->timeEntries()->where('status', 'completed')->sum('duration_minutes');
        $teamSize = $project->projectMembers()->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card 1: Budget Utilization --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Budget Utilization</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $estBudget ? format_currency($estBudget, $project->currency) : 'N/A' }}</span>
                <p class="text-xs text-slate-500 mt-1">Invoiced: <strong class="text-slate-700">{{ format_currency($actBudget, $project->currency) }}</strong></p>
            </div>
        </div>

        {{-- Card 2: Cost Utilization --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estimated Cost</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $estCost ? format_currency($estCost, $project->currency) : 'N/A' }}</span>
                <p class="text-xs text-slate-500 mt-1">Spent: <strong class="text-slate-700">{{ format_currency($actCost, $project->currency) }}</strong></p>
            </div>
        </div>

        {{-- Card 3: Tasks Health --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Task Completion</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $completedTasksCount }} / {{ $totalTasksCount }}</span>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">{{ $taskPct }}%</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Completed deliverables</p>
            </div>
        </div>

        {{-- Card 4: Team Squad & Logged Hours --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Squad & Time</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $teamSize }} members</span>
                <p class="text-xs text-slate-500 mt-1">Total Time: <strong class="text-slate-700">{{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m</strong></p>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    @php
        $canViewBudget = $project->hasPermissionForUser(auth()->user(), 'project.view-budget');
        $canViewTasks = $project->hasPermissionForUser(auth()->user(), 'project_task.view');
        $canViewDocs = $project->hasPermissionForUser(auth()->user(), 'document.view');
        $canViewMilestones = $project->hasPermissionForUser(auth()->user(), 'milestone.view');
        $canViewTime = $project->hasPermissionForUser(auth()->user(), 'time.view');
        $canViewMaterials = $project->hasPermissionForUser(auth()->user(), 'material_request.view');

        $tabs = [
            [
                'id' => 'overview', 
                'label' => 'Overview', 
                'visible' => true,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
            ],
            [
                'id' => 'team', 
                'label' => 'Team', 
                'count' => $project->projectMembers()->count(),
                'visible' => true,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
            ],
            [
                'id' => 'tasks', 
                'label' => 'Tasks', 
                'count' => $project->tasks()->count(),
                'visible' => $canViewTasks,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>'
            ],
            [
                'id' => 'timeline', 
                'label' => 'Timeline', 
                'visible' => true,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'id' => 'budget', 
                'label' => 'Budget', 
                'visible' => $canViewBudget,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'id' => 'milestones', 
                'label' => 'Milestones', 
                'count' => $project->milestones()->count(),
                'visible' => $canViewMilestones,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>'
            ],
            [
                'id' => 'documents', 
                'label' => 'Documents', 
                'count' => $project->documents()->count(),
                'visible' => $canViewDocs,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>'
            ],
            [
                'id' => 'discussions', 
                'label' => 'Discussions', 
                'count' => $project->comments()->count(),
                'visible' => true,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'
            ],
            [
                'id' => 'time_tracking', 
                'label' => 'Time Log', 
                'visible' => $canViewTime,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'id' => 'materials', 
                'label' => 'Materials', 
                'count' => $project->materialRequests()->count(),
                'visible' => $canViewMaterials,
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'
            ],
        ];
    @endphp

    <div x-data="{ activeTab: '{{ session('activeTab', 'overview') }}' }" class="mb-8">
        <div class="bg-white p-1.5 rounded-2xl shadow-xs border border-slate-200/80 mb-6 overflow-x-auto">
            <nav class="flex items-center gap-1 min-w-max" aria-label="Tabs">
                @foreach($tabs as $tab)
                    @if($tab['visible'])
                        <button @click="activeTab = '{{ $tab['id'] }}'" 
                                :class="{ 
                                    'bg-slate-900 text-white font-bold shadow-xs': activeTab === '{{ $tab['id'] }}', 
                                    'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium': activeTab !== '{{ $tab['id'] }}' 
                                }" 
                                class="whitespace-nowrap px-3.5 py-2 rounded-xl text-xs transition-all duration-200 flex items-center gap-2 focus:outline-none cursor-pointer">
                            {!! $tab['icon'] !!}
                            <span>{{ $tab['label'] }}</span>
                            @if(isset($tab['count']) && $tab['count'] > 0)
                                <span :class="{ 'bg-white/20 text-white': activeTab === '{{ $tab['id'] }}', 'bg-slate-100 text-slate-700': activeTab !== '{{ $tab['id'] }}' }" 
                                      class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-bold">
                                    {{ $tab['count'] }}
                                </span>
                            @endif
                        </button>
                    @endif
                @endforeach
            </nav>
        </div>
        
        <div class="mt-6">
            {{-- Overview Tab --}}
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Main Column (Left) --}}
                    <div class="lg:col-span-2 space-y-6">
                        
                        {{-- Description Card --}}
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 tracking-tight">Project Scope & Description</h3>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $project->description ?: 'No description provided for this project.' }}</p>
                        </div>
                        
                        {{-- Financials Overview --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estimated Budget</span>
                                <span class="block text-2xl font-extrabold text-slate-900 tracking-tight">{{ $project->estimated_budget ? format_currency($project->estimated_budget, $project->currency) : 'N/A' }}</span>
                                <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                                    <span class="font-medium text-slate-500">Actual Budget:</span>
                                    <span class="font-bold text-slate-900">{{ $project->actual_budget ? format_currency($project->actual_budget, $project->currency) : '—' }}</span>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estimated Cost</span>
                                <span class="block text-2xl font-extrabold text-slate-900 tracking-tight">{{ $project->estimated_cost ? format_currency($project->estimated_cost, $project->currency) : 'N/A' }}</span>
                                <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                                    <span class="font-medium text-slate-500">Actual Cost Spent:</span>
                                    <span class="font-bold text-slate-900">{{ $project->actual_cost ? format_currency($project->actual_cost, $project->currency) : '—' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Information Grid --}}
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 tracking-tight mb-4">Additional Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/60">
                                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Contract #</span>
                                    <span class="block text-xs font-bold text-slate-900 truncate" title="{{ $project->contract_number }}">{{ $project->contract_number ?? '—' }}</span>
                                </div>
                                <div class="p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/60">
                                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Reference #</span>
                                    <span class="block text-xs font-bold text-slate-900 truncate" title="{{ $project->reference_number }}">{{ $project->reference_number ?? '—' }}</span>
                                </div>
                                <div class="p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/60">
                                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Location</span>
                                    <span class="block text-xs font-bold text-slate-900 truncate" title="{{ $project->location }}">{{ $project->location ?? '—' }}</span>
                                </div>
                            </div>
                            
                            @if($project->notes)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Project Notes</span>
                                <div class="bg-amber-50/50 border border-amber-200/60 p-4 rounded-xl text-xs text-amber-900 font-medium leading-relaxed">
                                    {{ $project->notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Sidebar Column (Right) --}}
                    <div class="space-y-6">
                        {{-- People & Organization --}}
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                            <h3 class="text-base font-bold text-slate-900 tracking-tight mb-4">Key People & Squad</h3>
                            <ul class="space-y-4">
                                @php
                                    $myMember = $project->projectMembers?->firstWhere('user_id', auth()->id());
                                    $myRoleOnProject = $myMember?->project_role ?? (auth()->user()?->hasRole('Super Admin') || auth()->user()?->hasRole('CEO') ? 'Super Admin' : null);
                                @endphp

                                @if($myRoleOnProject)
                                    <li class="flex items-center gap-3 p-3 bg-blue-50/60 rounded-xl border border-blue-100">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider">Your Assigned Role</span>
                                            <span class="block text-xs font-bold text-blue-900 truncate">{{ $myRoleOnProject }}</span>
                                        </div>
                                    </li>
                                @endif
                                <li class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        @php
                                            $pmMember = $project->projectMembers?->firstWhere('user_id', $project->project_manager_id);
                                        @endphp
                                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $pmMember?->project_role ?? 'Project Manager' }}</span>
                                        <span class="block text-xs font-bold text-slate-900 truncate">{{ $project->manager?->full_name ?? $project->manager?->name ?? 'Not Assigned' }}</span>
                                    </div>
                                </li>
                                
                                <li class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Client</span>
                                        <span class="block text-xs font-bold text-slate-900 truncate">{{ $project->client?->display_name ?? 'Not Assigned' }}</span>
                                    </div>
                                </li>

                                <li class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Time Logged</span>
                                        <span class="block text-sm font-extrabold text-slate-900">
                                            {{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        {{-- System Information Card --}}
                        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-200/70">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                System Information
                            </h3>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-500">Created:</span>
                                    <span class="text-slate-900 font-semibold text-right">{{ $project->created_at?->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-500">Updated:</span>
                                    <span class="text-slate-900 font-semibold text-right">{{ $project->updated_at?->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Team Tab --}}
            <div x-show="activeTab === 'team'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                @include('admin.projects.team.partials.team_tab', ['project' => $project])
            </div>
            
            {{-- Tasks Tab --}}
            <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                @include('admin.projects.tasks.partials.tasks_tab', ['project' => $project])
            </div>
            
            {{-- Documents Tab --}}
            <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                @include('admin.documents.partials.document_tab', ['documentable' => $project])
            </div>
            
            {{-- Milestones Tab --}}
            <div x-show="activeTab === 'milestones'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                <x-card>
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Project Milestones</h3>
                            <p class="text-xs text-slate-500">Track major delivery phases and milestone deadlines.</p>
                        </div>
                        @can('create', [App\Models\Milestone::class, $project])
                            <x-button type="primary" href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" size="sm">
                                Create Milestone
                            </x-button>
                        @endcan
                    </div>
                    
                    <x-table>
                        <x-slot:head>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Milestone Name</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Progress</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Due Date</th>
                        </x-slot:head>

                        @forelse($project->milestones as $milestone)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.milestones.show', $milestone) }}" class="text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                        {{ $milestone->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-badge :type="match($milestone->status) { 'Completed' => 'success', 'In Progress' => 'primary', default => 'default' }">
                                        {{ $milestone->status }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden w-28">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $milestone->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700">{{ $milestone->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-semibold {{ $milestone->due_date && $milestone->due_date->isPast() && $milestone->status !== 'Completed' ? 'text-rose-600' : 'text-slate-900' }}">{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-500">
                                    No milestones created for this project yet.
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>
            
            {{-- Time Tracking Tab --}}
            <div x-show="activeTab === 'time_tracking'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                <x-card>
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Project Time Log</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Total time logged: <strong class="text-slate-900">{{ intdiv($project->timeEntries()->where('status', 'completed')->sum('duration_minutes'), 60) }}h {{ $project->timeEntries()->where('status', 'completed')->sum('duration_minutes') % 60 }}m</strong></p>
                        </div>
                        <a href="{{ route('admin.time-tracking.index', ['project_id' => $project->id]) }}" class="text-xs font-bold text-blue-600 hover:underline">View Detailed Timesheet &rarr;</a>
                    </div>
                    
                    <x-table>
                        <x-slot:head>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">User</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Duration</th>
                        </x-slot:head>
                        @forelse($project->timeEntries()->where('status', 'completed')->with('user')->latest()->take(5)->get() as $entry)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 text-sm font-bold text-slate-900">{{ $entry->user->full_name ?? $entry->user->name }}</td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-600">{{ $entry->start_time->format('M d, Y') }}</td>
                                <td class="px-5 py-3.5 text-xs font-bold text-slate-900">{{ intdiv($entry->duration_minutes, 60) }}h {{ $entry->duration_minutes % 60 }}m</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-slate-500">
                                    No time logged for this project yet.
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>
            
            {{-- Materials Tab --}}
            <div x-show="activeTab === 'materials'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;" x-cloak>
                @include('admin.projects.partials.materials_tab', ['project' => $project])
            </div>
            
            {{-- Timeline Tab --}}
            <div x-show="activeTab === 'timeline'" style="display: none;" x-cloak>
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Project Activity Timeline</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Chronological events and updates on this project</p>
                        </div>
                        <a href="{{ route('admin.projects.timeline', $project) }}" class="text-xs font-bold text-blue-600 hover:underline">Full Screen Timeline &rarr;</a>
                    </div>
                    
                    <div class="relative border-l-2 border-slate-200 ml-4 space-y-6">
                        @forelse($timelineEvents->take(10) as $event)
                            <div class="pl-6 relative group">
                                <div class="absolute w-8 h-8 rounded-lg -left-[17px] bg-white border-2 flex items-center justify-center shadow-xs transition-transform group-hover:scale-110
                                    {{ $event['type'] == 'created' ? 'border-blue-500 text-blue-600 bg-blue-50' : '' }}
                                    {{ $event['type'] == 'closed' ? 'border-emerald-500 text-emerald-600 bg-emerald-50' : '' }}
                                    {{ $event['type'] == 'success' ? 'border-green-500 text-green-600 bg-green-50' : '' }}
                                    {{ $event['type'] == 'info' ? 'border-purple-500 text-purple-600 bg-purple-50' : 'border-slate-300 text-slate-400' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($event['icon'] == 'plus')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        @elseif($event['icon'] == 'check-circle' || $event['icon'] == 'check')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        @elseif($event['icon'] == 'flag')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                        @elseif($event['icon'] == 'clipboard-list')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        @elseif($event['icon'] == 'document')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </div>
                                
                                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/60 shadow-xs">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1 gap-1">
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $event['title'] }}</h4>
                                        <span class="text-[10px] font-bold text-slate-500 whitespace-nowrap bg-white px-2 py-0.5 rounded-md border border-slate-200">{{ $event['date']->format('F j, Y h:i A') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium">{{ $event['description'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-xs font-medium text-slate-500">No timeline events recorded yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Budget Tab --}}
            <div x-show="activeTab === 'budget'" style="display: none;" x-cloak>
                @php
                    $budgetUsedPct = $estBudget > 0 ? min(100, round(($actBudget / $estBudget) * 100)) : 0;
                    $costUsedPct = $estCost > 0 ? min(100, round(($actCost / $estCost) * 100)) : 0;
                    
                    $estProfit = $estBudget - $estCost;
                    $actProfit = $actBudget - $actCost;
                    $estMargin = $estBudget > 0 ? round(($estProfit / $estBudget) * 100, 1) : 0;
                    $actMargin = $actBudget > 0 ? round(($actProfit / $actBudget) * 100, 1) : 0;
                @endphp
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Budget Tracking --}}
                    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Budget Tracking</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Estimated Budget</span>
                                        <span class="block text-2xl font-extrabold text-slate-900">{{ format_currency($estBudget, $project->currency) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Actual Invoiced</span>
                                        <span class="block text-lg font-bold text-emerald-600">{{ format_currency($actBudget, $project->currency) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-700" style="width: {{ $budgetUsedPct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-xs font-semibold text-slate-500">
                                    <span>0%</span>
                                    <span>{{ $budgetUsedPct }}% Achieved</span>
                                    <span>100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Cost Tracking --}}
                    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Cost Tracking</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Estimated Cost</span>
                                        <span class="block text-2xl font-extrabold text-slate-900">{{ format_currency($estCost, $project->currency) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Actual Spent</span>
                                        <span class="block text-lg font-bold {{ $actCost > $estCost ? 'text-rose-600' : 'text-rose-600' }}">{{ format_currency($actCost, $project->currency) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-rose-600 h-2.5 rounded-full transition-all duration-700" style="width: {{ $costUsedPct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-xs font-semibold text-slate-500">
                                    <span>0%</span>
                                    <span class="{{ $actCost > $estCost ? 'text-rose-600 font-bold' : 'text-slate-500' }}">{{ $costUsedPct }}% Consumed</span>
                                    <span>100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Profitability Analysis --}}
                    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Profitability Analysis</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="p-5 bg-slate-50/70 rounded-xl border border-slate-200/60">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estimated Profit</span>
                                        <span class="block text-2xl font-extrabold text-slate-900">{{ format_currency($estProfit, $project->currency) }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $estMargin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $estMargin }}% Margin
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-5 bg-slate-50/70 rounded-xl border border-slate-200/60">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Actual Profit</span>
                                        <span class="block text-2xl font-extrabold {{ $actProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ format_currency($actProfit, $project->currency) }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $actMargin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $actMargin }}% Margin
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity Tab --}}
            <div x-show="activeTab === 'activity'" style="display: none;" x-cloak>
                <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 tracking-tight">Audit & Activity Log</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Audit trail of actions taken on this project</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        @forelse($activityLogs as $log)
                            <div class="flex items-start gap-3 p-3.5 rounded-xl hover:bg-slate-50 transition-colors border border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-600 font-bold text-xs">
                                    {{ substr($log->user?->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-900 font-medium">
                                        <strong class="font-bold text-slate-900">{{ $log->user?->name ?? 'System' }}</strong>
                                        {{ $log->action }}
                                        @if(isset($log->properties['attributes']))
                                            <span class="text-slate-500 line-clamp-1 mt-0.5 text-[11px] font-mono">{{ json_encode($log->properties['attributes']) }}</span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-medium mt-1">
                                        {{ $log->created_at->diffForHumans() }} • {{ $log->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-500 text-xs font-medium">
                                No activity logs recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Discussions Tab --}}
            <div x-show="activeTab === 'discussions'" style="display: none;" x-cloak>
                <x-discussions :model="$project" />
            </div>
        </div>
    </div>
</x-layouts.admin>
