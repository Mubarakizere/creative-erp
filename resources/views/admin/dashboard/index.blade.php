<x-layouts.admin title="Dashboard">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->first_name }}! Here's what's happening today.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Projects --}}
        <x-stats-card
            title="Total Projects"
            value="{{ number_format($stats['projects']) }}"
            trend="+12%"
            :trend-up="true"
            color="blue"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </x-stats-card>

        {{-- Active Projects --}}
        <x-stats-card
            title="Active Projects"
            value="{{ number_format($stats['active_projects']) }}"
            trend="+5%"
            :trend-up="true"
            color="emerald"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </x-stats-card>

        {{-- Clients --}}
        <x-stats-card
            title="Total Clients"
            value="{{ number_format($stats['clients']) }}"
            trend="+8%"
            :trend-up="true"
            color="purple"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>

        {{-- Users --}}
        <x-stats-card
            title="Total Users"
            value="{{ number_format($stats['users']) }}"
            trend="+3%"
            :trend-up="true"
            color="amber"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Second Row Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        {{-- Companies --}}
        <x-stats-card
            title="Companies"
            value="{{ number_format($stats['companies']) }}"
            color="cyan"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>

        {{-- Branches --}}
        <x-stats-card
            title="Branches"
            value="{{ number_format($stats['branches']) }}"
            color="orange"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </x-stats-card>

        {{-- Departments --}}
        <x-stats-card
            title="Departments"
            value="{{ number_format($stats['departments']) }}"
            color="indigo"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </x-stats-card>

        {{-- Est Budget --}}
        <x-stats-card
            title="Total Est. Budget"
            value="{{ format_currency($stats['total_estimated_budget']) }}"
            color="rose"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        {{-- Act Budget --}}
        <x-stats-card
            title="Total Act. Budget"
            value="{{ format_currency($stats['total_actual_budget']) }}"
            color="emerald"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Third Row Stats: Project Teams --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <x-stats-card title="Total Members" value="{{ number_format($stats['total_team_members']) }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Active Members" value="{{ number_format($stats['active_team_members']) }}" color="emerald">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Inactive Members" value="{{ number_format($stats['inactive_team_members']) }}" color="amber">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Project Managers" value="{{ number_format($stats['project_managers']) }}" color="purple">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Engineers" value="{{ number_format($stats['engineers']) }}" color="cyan">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </x-stats-card>
        
        <x-stats-card title="Team Utilization" value="{{ $stats['total_team_members'] > 0 ? round(($stats['active_team_members'] / $stats['total_team_members']) * 100) : 0 }}%" color="indigo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </x-stats-card>
    </div>

    {{-- Bottom Section: Recent Projects + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Latest Projects --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Latest Projects</h3>
                        <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                    </div>
                </x-slot:header>

                <div class="space-y-4">
                    @forelse($latestProjects as $project)
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $project->name }}</a>
                                <p class="text-xs text-gray-500 mt-1">{{ $project->company?->name }} • {{ $project->created_at->diffForHumans() }}</p>
                            </div>
                            <x-badge :type="match($project->status) { 'Planning' => 'default', 'In Progress' => 'primary', 'Completed', 'Closed' => 'success', default => 'warning' }">
                                {{ $project->status }}
                            </x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">No projects created yet.</p>
                    @endforelse
                </div>
            </x-card>

            {{-- Recent Activity Feed (Sample Data) --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </x-slot:header>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">System</span> completed database backup.</p>
                            <p class="text-xs text-gray-500 mt-0.5">10 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">Admin User</span> added a new client <span class="font-medium">MTN Rwanda</span>.</p>
                            <p class="text-xs text-gray-500 mt-0.5">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">Project Manager</span> updated project status to <span class="font-medium">In Progress</span>.</p>
                            <p class="text-xs text-gray-500 mt-0.5">Yesterday at 4:30 PM</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Quick Actions & Project Summary --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </x-slot:header>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.projects.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 hover:text-blue-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Project</span>
                    </a>
                    <a href="{{ route('admin.projects.team.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 hover:text-blue-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Assign Member</span>
                    </a>
                    <a href="{{ route('admin.clients.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Client</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-purple-50 hover:text-purple-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">Add User</span>
                    </a>
                    <a href="{{ route('admin.companies.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-50 hover:bg-amber-50 hover:text-amber-700 text-gray-600 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium">New Company</span>
                    </a>
                </div>
            </x-card>

            {{-- Project Status Summary --}}
            <x-card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Projects by Status</h3>
                </x-slot:header>

                @php
                    $total = $stats['projects'] > 0 ? $stats['projects'] : 1;
                    $activePct = round(($stats['active_projects'] / $total) * 100);
                    $completedPct = round(($stats['completed_projects'] / $total) * 100);
                    $closedPct = round(($stats['closed_projects'] / $total) * 100);
                    $holdPct = round(($stats['on_hold_projects'] / $total) * 100);
                @endphp

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Active</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['active_projects']) }} ({{ $activePct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $activePct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Completed</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['completed_projects']) }} ({{ $completedPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $completedPct }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Closed</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['closed_projects']) }} ({{ $closedPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $closedPct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">On Hold</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['on_hold_projects']) }} ({{ $holdPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $holdPct }}%"></div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Latest Team Members --}}
    <div class="mt-8">
        <x-card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recently Assigned Team Members</h3>
                    <a href="{{ route('admin.projects.team.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All Team Members</a>
                </div>
            </x-slot:header>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Dept</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($latestTeamMembers ?? [] as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($member->user->avatar)
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $member->user->avatar) }}" alt="">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                    {{ $member->user->initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->user->full_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.projects.show', $member->project_id) }}" class="text-sm text-blue-600 hover:text-blue-900">{{ $member->project->name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $member->project_role }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->department->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->joined_at->format('M j, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No team members assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
