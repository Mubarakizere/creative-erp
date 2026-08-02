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

    {{-- Premium Page Header --}}
    <div class="relative bg-white overflow-hidden rounded-3xl shadow-sm border border-gray-100 mb-8 z-10 group">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 p-32 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full blur-3xl opacity-60 -z-10 group-hover:opacity-80 transition-opacity"></div>
        <div class="absolute bottom-0 left-0 p-24 bg-gradient-to-tr from-emerald-50 to-teal-50 rounded-full blur-3xl opacity-50 -z-10 group-hover:opacity-70 transition-opacity"></div>
        
        <div class="px-8 py-8 sm:py-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 text-xs font-bold text-blue-700 bg-blue-100 rounded-full">{{ $project->project_code }}</span>
                    <x-badge :type="match($project->status) { 'Completed', 'Closed' => 'success', 'Cancelled' => 'danger', 'In Progress' => 'primary', 'Pending' => 'warning', default => 'default' }">
                        {{ $project->status }}
                    </x-badge>
                    @if($project->priority === 'Critical')
                        <span class="px-3 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full animate-pulse flex items-center gap-1 shadow-sm border border-red-200">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Critical Priority
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-2">
                    {{ $project->name }}
                </h1>
                <div class="flex items-center text-sm font-medium text-gray-500 gap-4 mt-3">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> {{ $project->company?->name ?? 'N/A' }}</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $project->client?->display_name ?? 'No Client' }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                @can('view', $project)
                    <a href="{{ route('admin.projects.timeline', $project) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all focus:ring-2 focus:ring-blue-500 shadow-sm whitespace-nowrap w-full sm:w-auto hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Timeline
                    </a>
                @endcan
                
                @if(!$project->trashed())
                    @can('update', $project)
                        <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all focus:ring-2 focus:ring-blue-500 shadow-md shadow-blue-500/20 whitespace-nowrap w-full sm:w-auto transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Project
                        </a>
                    @endcan
                @endif
            </div>
        </div>
        
        <div class="px-8 py-5 bg-gray-50/80 border-t border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 backdrop-blur-sm">
            <div class="flex-1 w-full max-w-xl">
                <div class="flex justify-between text-xs font-bold text-gray-700 mb-2">
                    <span class="uppercase tracking-wider">Overall Progress</span>
                    <span class="text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $project->progress }}%"></div>
                </div>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="text-right hidden sm:block">
                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Start Date</span>
                    <span class="block text-sm font-bold text-gray-900">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="h-10 w-px bg-gray-200 hidden sm:block"></div>
                <div class="text-right">
                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Due Date</span>
                    <span class="block text-sm font-bold {{ $project->planned_end_date && $project->planned_end_date->isPast() && $project->status !== 'Completed' ? 'text-rose-600 bg-rose-50 px-2 py-0.5 rounded -mr-2' : 'text-gray-900' }}">
                        {{ $project->planned_end_date?->format('M d, Y') ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div x-data="{ activeTab: '{{ session('activeTab', 'overview') }}' }" class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-1.5 overflow-x-auto hide-scrollbar">
            <nav class="flex space-x-2 min-w-max" aria-label="Tabs">
                @php
                    $tabs = [
                        ['id' => 'overview', 'label' => 'Overview'],
                        ['id' => 'team', 'label' => 'Team'],
                        ['id' => 'timeline', 'label' => 'Timeline'],
                        ['id' => 'budget', 'label' => 'Budget'],
                        ['id' => 'activity', 'label' => 'Activity'],
                        ['id' => 'discussions', 'label' => 'Discussions', 'count' => $project->comments()->count()],
                        ['id' => 'documents', 'label' => 'Documents'],
                        ['id' => 'tasks', 'label' => 'Tasks'],
                        ['id' => 'milestones', 'label' => 'Milestones'],
                        ['id' => 'time_tracking', 'label' => 'Time Log'],
                        ['id' => 'materials', 'label' => 'Materials'],
                    ];
                @endphp
                
                @foreach($tabs as $tab)
                    <button @click="activeTab = '{{ $tab['id'] }}'" 
                            :class="{ 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100': activeTab === '{{ $tab['id'] }}', 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-transparent': activeTab !== '{{ $tab['id'] }}' }" 
                            class="whitespace-nowrap px-4 py-2.5 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                        {{ $tab['label'] }}
                        @if(isset($tab['count']) && $tab['count'] > 0)
                            <span :class="{ 'bg-blue-200 text-blue-800': activeTab === '{{ $tab['id'] }}', 'bg-gray-100 text-gray-700': activeTab !== '{{ $tab['id'] }}' }" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-black transition-colors">
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>
        
        <div class="mt-6">
            {{-- Overview Tab --}}
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        
                        {{-- Description Card --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="flex items-center gap-3 mb-5 relative z-10">
                                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100/50 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900">Project Description</h3>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap relative z-10">{{ $project->description ?: 'No description provided.' }}</p>
                        </div>
                        
                        {{-- Financials Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                                <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                                    <svg class="w-32 h-32 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Estimated Budget</span>
                                <span class="block text-3xl font-black text-gray-900 tracking-tight relative z-10">{{ $project->estimated_budget ? format_currency($project->estimated_budget, $project->currency) : 'N/A' }}</span>
                                <div class="mt-5 flex items-center justify-between text-sm pt-4 border-t border-gray-50 relative z-10">
                                    <span class="font-semibold text-gray-500">Actual Budget</span>
                                    <span class="font-bold text-gray-900">{{ $project->actual_budget ? format_currency($project->actual_budget, $project->currency) : '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                                <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500">
                                    <svg class="w-32 h-32 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                </div>
                                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 relative z-10">Estimated Cost</span>
                                <span class="block text-3xl font-black text-gray-900 tracking-tight relative z-10">{{ $project->estimated_cost ? format_currency($project->estimated_cost, $project->currency) : 'N/A' }}</span>
                                <div class="mt-5 flex items-center justify-between text-sm pt-4 border-t border-gray-50 relative z-10">
                                    <span class="font-semibold text-gray-500">Actual Cost</span>
                                    <span class="font-bold text-gray-900">{{ $project->actual_cost ? format_currency($project->actual_cost, $project->currency) : '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Details --}}
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <h3 class="text-lg font-extrabold text-gray-900 mb-5">Additional Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-100/80">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Contract #</span>
                                    <span class="block text-sm font-bold text-gray-900 truncate" title="{{ $project->contract_number }}">{{ $project->contract_number ?? 'N/A' }}</span>
                                </div>
                                <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-100/80">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Reference #</span>
                                    <span class="block text-sm font-bold text-gray-900 truncate" title="{{ $project->reference_number }}">{{ $project->reference_number ?? 'N/A' }}</span>
                                </div>
                                <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-100/80">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Location</span>
                                    <span class="block text-sm font-bold text-gray-900 truncate" title="{{ $project->location }}">{{ $project->location ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            @if($project->notes)
                            <div class="mt-6">
                                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Notes</span>
                                <div class="bg-yellow-50/50 border border-yellow-100 p-5 rounded-2xl shadow-sm">
                                    <p class="text-sm text-yellow-800 whitespace-pre-wrap leading-relaxed font-medium">{{ $project->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        {{-- People & Organization --}}
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <h3 class="text-lg font-extrabold text-gray-900 mb-5">Key People</h3>
                            <ul class="space-y-5">
                                <li class="flex items-center gap-4 group">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-700 flex-shrink-0 shadow-sm border border-emerald-200 group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Project Manager</span>
                                        <span class="block text-sm font-bold text-gray-900 truncate">{{ $project->manager?->full_name ?? $project->manager?->name ?? 'Not Assigned' }}</span>
                                    </div>
                                </li>
                                
                                <li class="flex items-center gap-4 group">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-100 to-fuchsia-100 flex items-center justify-center text-purple-700 flex-shrink-0 shadow-sm border border-purple-200 group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Client</span>
                                        <span class="block text-sm font-bold text-gray-900 truncate">{{ $project->client?->display_name ?? 'Not Assigned' }}</span>
                                    </div>
                                </li>
                                
                                <li class="flex items-center gap-4 pt-5 border-t border-gray-100/80">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-700 flex-shrink-0 shadow-sm border border-blue-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Total Time Logged</span>
                                        <span class="block text-lg font-black text-gray-900">
                                            @php
                                                $totalMinutes = $project->timeEntries()->where('status', 'completed')->sum('duration_minutes');
                                            @endphp
                                            {{ intdiv($totalMinutes, 60) }}<span class="text-sm font-medium text-gray-500 ml-0.5">h</span> {{ $totalMinutes % 60 }}<span class="text-sm font-medium text-gray-500 ml-0.5">m</span>
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        {{-- System Info --}}
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-200/60 shadow-sm">
                            <h3 class="text-sm font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                System Information
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-gray-500 uppercase">Created</span>
                                    <span class="text-gray-900 font-bold text-right">{{ $project->created_at?->format('M d, Y H:i') }} <br><span class="text-gray-500 font-semibold">by {{ $project->creator?->full_name ?? $project->creator?->name ?? 'System' }}</span></span>
                                </div>
                                <div class="border-t border-gray-200/60"></div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-gray-500 uppercase">Updated</span>
                                    <span class="text-gray-900 font-bold text-right">{{ $project->updated_at?->format('M d, Y H:i') }} <br><span class="text-gray-500 font-semibold">by {{ $project->updater?->full_name ?? $project->updater?->name ?? 'System' }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Team Tab --}}
            <div x-show="activeTab === 'team'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                @include('admin.projects.team.partials.team_tab', ['project' => $project])
            </div>
            
            {{-- Tasks Tab --}}
            <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                @include('admin.projects.tasks.partials.tasks_tab', ['project' => $project])
            </div>
            
            {{-- Documents Tab --}}
            <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                @include('admin.documents.partials.document_tab', ['documentable' => $project])
            </div>
            
            {{-- Milestones Tab --}}
            <div x-show="activeTab === 'milestones'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                <x-card>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-extrabold text-gray-900">Project Milestones</h3>
                        @can('create', App\Models\Milestone::class)
                            <x-button type="primary" href="{{ route('admin.milestones.create') }}?project_id={{ $project->id }}" size="sm">
                                Create Milestone
                            </x-button>
                        @endcan
                    </div>
                    
                    <x-table>
                        <x-slot:head>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Milestone Name</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                        </x-slot:head>

                        @forelse($project->milestones as $milestone)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.milestones.show', $milestone) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                        {{ $milestone->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">{{ $milestone->status }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden w-32 shadow-inner">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $milestone->progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-700">{{ $milestone->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-sm font-semibold {{ $milestone->due_date && $milestone->due_date->isPast() && $milestone->status !== 'Completed' ? 'text-red-600' : 'text-gray-900' }}">{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <p class="text-sm font-medium text-gray-500">No milestones created for this project yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>
            
            {{-- Time Tracking Tab --}}
            <div x-show="activeTab === 'time_tracking'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                <x-card>
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900">Project Time Log</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Total time logged: <span class="font-bold text-gray-900">{{ intdiv($project->timeEntries()->where('status', 'completed')->sum('duration_minutes'), 60) }}h {{ $project->timeEntries()->where('status', 'completed')->sum('duration_minutes') % 60 }}m</span></p>
                        </div>
                        <a href="{{ route('admin.time-tracking.index', ['project_id' => $project->id]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-bold transition-colors">View Detailed Report &rarr;</a>
                    </div>
                    
                    <x-table>
                        <x-slot:head>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                        </x-slot:head>
                        @forelse($project->timeEntries()->where('status', 'completed')->with('user')->latest()->take(5)->get() as $entry)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $entry->user->full_name }}</td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-700">{{ $entry->start_time->format('M d, Y') }}</td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 bg-gray-50/50">{{ intdiv($entry->duration_minutes, 60) }}h {{ $entry->duration_minutes % 60 }}m</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-sm font-medium text-gray-500">No time logged for this project yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </x-card>
            </div>
            
            {{-- Materials Tab --}}
            <div x-show="activeTab === 'materials'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" x-cloak>
                @include('admin.projects.partials.materials_tab', ['project' => $project])
            </div>
            
            {{-- Timeline Tab --}}
            <div x-show="activeTab === 'timeline'" style="display: none;" x-cloak>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900">Project Timeline</h3>
                            <p class="text-sm text-gray-500 font-medium mt-1">Recent events and activities on this project</p>
                        </div>
                        <a href="{{ route('admin.projects.timeline', $project) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">View Full Screen &rarr;</a>
                    </div>
                    
                    <div class="relative border-l-2 border-gray-200 ml-4">
                        @forelse($timelineEvents->take(10) as $event)
                            <div class="mb-8 pl-8 relative group">
                                <div class="absolute w-10 h-10 rounded-xl -left-[21px] bg-white border-2 flex items-center justify-center shadow-sm transition-transform group-hover:scale-110
                                    {{ $event['type'] == 'created' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : '' }}
                                    {{ $event['type'] == 'closed' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50' : '' }}
                                    {{ $event['type'] == 'success' ? 'border-green-500 text-green-600 bg-green-50/50' : '' }}
                                    {{ $event['type'] == 'info' ? 'border-purple-500 text-purple-600 bg-purple-50/50' : 'border-gray-200 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                
                                <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-2">
                                        <h3 class="font-bold text-gray-900 text-base">{{ $event['title'] }}</h3>
                                        <span class="text-xs font-bold text-gray-500 whitespace-nowrap bg-white px-2.5 py-1 rounded-lg border border-gray-200/60">{{ $event['date']->format('F j, Y h:i A') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 font-medium">{{ $event['description'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-sm font-medium text-gray-500">No events recorded yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'budget'" style="display: none;" x-cloak>
                @php
                    $estBudget = $project->estimated_budget ?? 0;
                    $actBudget = $project->actual_budget ?? 0;
                    $estCost = $project->estimated_cost ?? 0;
                    $actCost = $project->actual_cost ?? 0;

                    $budgetUsedPct = $estBudget > 0 ? min(100, round(($actBudget / $estBudget) * 100)) : 0;
                    $costUsedPct = $estCost > 0 ? min(100, round(($actCost / $estCost) * 100)) : 0;
                    
                    $estProfit = $estBudget - $estCost;
                    $actProfit = $actBudget - $actCost;
                    $estMargin = $estBudget > 0 ? round(($estProfit / $estBudget) * 100, 1) : 0;
                    $actMargin = $actBudget > 0 ? round(($actProfit / $actBudget) * 100, 1) : 0;
                @endphp
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Budget Tracking --}}
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 opacity-[0.03] group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 pointer-events-none">
                            <svg class="w-48 h-48 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        
                        <div class="flex items-center gap-3 mb-6 relative z-10">
                            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100/50 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900">Budget Tracking</h3>
                        </div>
                        
                        <div class="space-y-6 relative z-10">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Estimated Budget</span>
                                        <span class="block text-2xl font-black text-gray-900">{{ format_currency($estBudget, $project->currency) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Actual Invoiced</span>
                                        <span class="block text-xl font-bold text-emerald-600">{{ format_currency($actBudget, $project->currency) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 shadow-inner overflow-hidden flex items-center">
                                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $budgetUsedPct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-xs font-bold text-gray-500">
                                    <span>0%</span>
                                    <span>{{ $budgetUsedPct }}% Achieved</span>
                                    <span>100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Cost Tracking --}}
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 opacity-[0.03] group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 pointer-events-none">
                            <svg class="w-48 h-48 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                        
                        <div class="flex items-center gap-3 mb-6 relative z-10">
                            <div class="p-2.5 bg-rose-50 text-rose-600 rounded-xl border border-rose-100/50 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900">Cost Tracking</h3>
                        </div>
                        
                        <div class="space-y-6 relative z-10">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Estimated Cost</span>
                                        <span class="block text-2xl font-black text-gray-900">{{ format_currency($estCost, $project->currency) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Actual Spent</span>
                                        <span class="block text-xl font-bold {{ $actCost > $estCost ? 'text-red-600' : 'text-rose-600' }}">{{ format_currency($actCost, $project->currency) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 shadow-inner overflow-hidden flex items-center">
                                    <div class="bg-gradient-to-r {{ $actCost > $estCost ? 'from-red-500 to-red-700' : 'from-rose-400 to-rose-600' }} h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $costUsedPct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-xs font-bold">
                                    <span class="text-gray-500">0%</span>
                                    <span class="{{ $actCost > $estCost ? 'text-red-600' : 'text-gray-500' }}">{{ $costUsedPct }}% Consumed</span>
                                    <span class="text-gray-500">100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Profitability Analysis --}}
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100/50 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900">Profitability Analysis</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="p-6 bg-gray-50/80 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimated Profit</span>
                                        <span class="block text-3xl font-black text-gray-900">{{ format_currency($estProfit, $project->currency) }}</span>
                                    </div>
                                    <div class="px-3 py-1.5 rounded-lg font-bold text-sm {{ $estMargin >= 0 ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                        {{ $estMargin }}% Margin
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50/80 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Actual Profit</span>
                                        <span class="block text-3xl font-black {{ $actProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ format_currency($actProfit, $project->currency) }}</span>
                                    </div>
                                    <div class="px-3 py-1.5 rounded-lg font-bold text-sm {{ $actMargin >= 0 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                        {{ $actMargin }}% Margin
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'activity'" style="display: none;" x-cloak>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl border border-purple-100/50 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900">Activity Log</h3>
                            <p class="text-sm text-gray-500 font-medium mt-1">Detailed audit trail of actions taken on this project</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($activityLogs as $log)
                            <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center flex-shrink-0 text-gray-600 font-bold text-sm shadow-inner border border-gray-200">
                                    {{ substr($log->user?->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 font-medium">
                                        <span class="font-bold">{{ $log->user?->name ?? 'System' }}</span>
                                        {{ $log->action }}
                                        @if(isset($log->properties['attributes']))
                                            <span class="text-gray-500 line-clamp-1 mt-1 text-xs">{{ json_encode($log->properties['attributes']) }}</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 font-medium mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $log->created_at->diffForHumans() }} 
                                        <span class="text-gray-300 mx-1">•</span> 
                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">No activity logs recorded yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'discussions'" style="display: none;" x-cloak>
                <x-discussions :model="$project" />
            </div>
        </div>
    </div>
</x-layouts.admin>
