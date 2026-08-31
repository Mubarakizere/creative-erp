<x-layouts.admin title="Time Reports">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Time Tracking', 'url' => route('admin.time-tracking.index')],
                ['label' => 'Reports'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Time & Attendance Reports</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Analyze team hours, project time, and attendance trends.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('create', \App\Models\ReportTemplate::class)
                <a href="{{ route('admin.reports.builder') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors whitespace-nowrap min-h-[42px] w-full sm:w-auto">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Custom Report
                </a>
            @endcan
            
            <form action="{{ route('admin.time-tracking.reports') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="alert('Export functionality coming in future sprint')" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors whitespace-nowrap min-h-[42px]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </button>
        </div>
    </div>

    @php
        $projects = auth()->user()->accessibleProjects()
            ->withSum(['timeEntries' => fn($q) => $q->where('status', 'completed')], 'duration_minutes')
            ->get();
        
        $users = \App\Models\User::where('company_id', auth()->user()->company_id)
            ->withSum(['timeEntries' => fn($q) => $q->where('status', 'completed')], 'duration_minutes')
            ->get();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Charts Area --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6 lg:p-8">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Project Distribution</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="projectChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-6 lg:p-8">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Team Member Contribution</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="teamChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Project List --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Project Summary</h3>
                <span class="inline-flex rounded-md px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider leading-5 bg-blue-50 text-blue-700 border border-blue-100">
                    Hours Logged
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($projects as $project)
                    @if($project->time_entries_sum_duration_minutes > 0)
                        <div class="flex justify-between items-center px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-3"></div>
                                <span class="text-sm font-bold text-gray-900">{{ $project->name }}</span>
                            </div>
                            <span class="text-sm text-gray-700 font-bold bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                                {{ intdiv($project->time_entries_sum_duration_minutes ?? 0, 60) }}h {{ ($project->time_entries_sum_duration_minutes ?? 0) % 60 }}m
                            </span>
                        </div>
                    @endif
                @empty
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">No time entries found for projects.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Team List --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Team Summary</h3>
                <span class="inline-flex rounded-md px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider leading-5 bg-purple-50 text-purple-700 border border-purple-100">
                    Hours Logged
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($users as $user)
                    @if($user->time_entries_sum_duration_minutes > 0)
                        <div class="flex justify-between items-center px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold mr-3 border border-purple-200">
                                    {{ substr($user->full_name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $user->full_name }}</span>
                            </div>
                            <span class="text-sm text-gray-700 font-bold bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">
                                {{ intdiv($user->time_entries_sum_duration_minutes ?? 0, 60) }}h {{ ($user->time_entries_sum_duration_minutes ?? 0) % 60 }}m
                            </span>
                        </div>
                    @endif
                @empty
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">No time entries found for team members.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
            Chart.defaults.color = '#6b7280';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.9)';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;

            const projectsRaw = @json($projects->filter(fn($p) => $p->time_entries_sum_duration_minutes > 0)->values());
            const usersRaw = @json($users->filter(fn($u) => $u->time_entries_sum_duration_minutes > 0)->values());

            // Project Chart (Pie)
            if(projectsRaw.length > 0) {
                const projCtx = document.getElementById('projectChart').getContext('2d');
                new Chart(projCtx, {
                    type: 'doughnut',
                    data: {
                        labels: projectsRaw.map(p => p.name),
                        datasets: [{
                            data: projectsRaw.map(p => (p.time_entries_sum_duration_minutes / 60).toFixed(2)),
                            backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6366f1'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, padding: 15 } },
                            tooltip: { callbacks: { label: function(context) { return context.label + ': ' + context.raw + ' hrs'; } } }
                        }
                    }
                });
            } else {
                document.getElementById('projectChart').parentElement.innerHTML = '<p class="text-sm text-gray-400">No project data available.</p>';
            }

            // Team Chart (Bar)
            if(usersRaw.length > 0) {
                const teamCtx = document.getElementById('teamChart').getContext('2d');
                new Chart(teamCtx, {
                    type: 'bar',
                    data: {
                        labels: usersRaw.map(u => u.full_name || u.first_name + ' ' + u.last_name || u.name),
                        datasets: [{
                            label: 'Hours Logged',
                            data: usersRaw.map(u => (u.time_entries_sum_duration_minutes / 60).toFixed(2)),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6', drawBorder: false } },
                            x: { grid: { display: false, drawBorder: false } }
                        }
                    }
                });
            } else {
                document.getElementById('teamChart').parentElement.innerHTML = '<p class="text-sm text-gray-400">No team data available.</p>';
            }
        });
    </script>
</x-layouts.admin>
