<x-layouts.admin title="Time Reports">
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
            <h1 class="text-2xl font-bold text-gray-900">Time Reports</h1>
            <p class="mt-1 text-sm text-gray-500">Generate and export time tracking reports.</p>
        </div>
        
        <div class="flex gap-2">
            <x-button type="primary" onclick="alert('Export functionality coming in future sprint')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Summary</h3>
            <p class="text-sm text-gray-500 mb-4">A breakdown of hours logged per project.</p>
            <div class="space-y-4">
                @foreach(\App\Models\Project::where('company_id', auth()->user()->company_id)->withSum(['timeEntries' => fn($q) => $q->where('status', 'completed')], 'duration_minutes')->get() as $project)
                    <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $project->name }}</span>
                        <span class="text-sm text-gray-900 font-bold">{{ intdiv($project->time_entries_sum_duration_minutes ?? 0, 60) }}h {{ ($project->time_entries_sum_duration_minutes ?? 0) % 60 }}m</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Summary</h3>
            <p class="text-sm text-gray-500 mb-4">A breakdown of hours logged per team member.</p>
            <div class="space-y-4">
                @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->withSum(['timeEntries' => fn($q) => $q->where('status', 'completed')], 'duration_minutes')->get() as $user)
                    <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $user->full_name }}</span>
                        <span class="text-sm text-gray-900 font-bold">{{ intdiv($user->time_entries_sum_duration_minutes ?? 0, 60) }}h {{ ($user->time_entries_sum_duration_minutes ?? 0) % 60 }}m</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-layouts.admin>
