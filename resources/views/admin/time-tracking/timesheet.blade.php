<x-layouts.admin title="My Timesheet">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Time Tracking', 'url' => route('admin.time-tracking.index')],
                ['label' => 'My Timesheet'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Timesheet</h1>
            <p class="mt-1 text-sm text-gray-500">View and manage your tracked time.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="bg-gray-100 p-1 rounded-lg flex text-sm font-medium">
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'daily']) }}" 
                   @class(['px-3 py-1.5 rounded-md transition-colors', 'bg-white shadow-sm text-gray-900' => $period === 'daily', 'text-gray-500 hover:text-gray-900' => $period !== 'daily'])>
                    Daily
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'weekly']) }}" 
                   @class(['px-3 py-1.5 rounded-md transition-colors', 'bg-white shadow-sm text-gray-900' => $period === 'weekly', 'text-gray-500 hover:text-gray-900' => $period !== 'weekly'])>
                    Weekly
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'monthly']) }}" 
                   @class(['px-3 py-1.5 rounded-md transition-colors', 'bg-white shadow-sm text-gray-900' => $period === 'monthly', 'text-gray-500 hover:text-gray-900' => $period !== 'monthly'])>
                    Monthly
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'all']) }}" 
                   @class(['px-3 py-1.5 rounded-md transition-colors', 'bg-white shadow-sm text-gray-900' => $period === 'all', 'text-gray-500 hover:text-gray-900' => $period !== 'all'])>
                    All Time
                </a>
            </div>
            
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-2 text-right">
                <span class="block text-xs font-semibold text-blue-600 uppercase">Total Logged</span>
                <span class="block text-lg font-bold text-blue-900">
                    {{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m
                </span>
            </div>

            @can('create', App\Models\TimeEntry::class)
            <x-button type="primary" x-data @click="$dispatch('open-modal', 'create-time-entry')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Log Time
            </x-button>
            @endcan
        </div>
    </div>

    <!-- Stats summary could go here (hours today, week, month) -->
    
    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Project / Task</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Billable</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
        </x-slot:head>

        @forelse($entries as $entry)
            <tr>
                <td class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-900">{{ $entry->project->name }}</p>
                    @if($entry->task)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $entry->task->title }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-900">{{ $entry->start_time->format('M d, Y') }}</span>
                    <p class="text-xs text-gray-500">{{ $entry->start_time->format('h:i A') }} - {{ $entry->end_time->format('h:i A') }}</p>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm font-medium text-gray-900">{{ intdiv($entry->duration_minutes, 60) }}h {{ $entry->duration_minutes % 60 }}m</span>
                </td>
                <td class="px-4 py-3">
                    <x-badge :type="$entry->billable ? 'success' : 'default'">{{ $entry->billable ? 'Billable' : 'Non-Billable' }}</x-badge>
                </td>
                <td class="px-4 py-3 text-right">
                    @can('delete', $entry)
                        <button x-data type="button" @click="$dispatch('open-modal', 'delete-time-entry-{{ $entry->id }}')" class="text-red-500 hover:text-red-700 ml-2">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endcan
                </td>
            </tr>

            {{-- Delete Modal --}}
            <x-modal id="delete-time-entry-{{ $entry->id }}" maxWidth="md">
                <x-slot:header>Delete Time Entry</x-slot:header>
                <div class="text-center py-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Time Entry?</h3>
                    <p class="text-sm text-gray-500">Are you sure you want to delete this time entry? This action cannot be undone.</p>
                </div>
                <x-slot:footer>
                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                    <form method="POST" action="{{ route('admin.time-tracking.destroy', $entry) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" submit>Delete Entry</x-button>
                    </form>
                </x-slot:footer>
            </x-modal>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-12 text-center text-gray-500">You have no time entries yet.</td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $entries->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    <!-- Create Modal -->
    <x-modal id="create-time-entry" maxWidth="md">
        <x-slot:header>Log Time</x-slot:header>
        <form method="POST" action="{{ route('admin.time-tracking.store') }}">
            @csrf
            <div class="space-y-4 py-4 px-1">
                <x-project-task-search />
                <div class="grid grid-cols-2 gap-4">
                    <x-input type="datetime-local" name="start_time" label="Start Time" required />
                    <x-input type="datetime-local" name="end_time" label="End Time" required />
                </div>
                <x-textarea name="description" label="Description" rows="2" />
                <div class="flex items-center mt-2">
                    <input type="hidden" name="billable" value="0">
                    <input type="checkbox" name="billable" id="billable" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="billable" class="ml-2 block text-sm text-gray-900">Billable</label>
                </div>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Save Entry</x-button>
            </x-slot:footer>
        </form>
    </x-modal>
</x-layouts.admin>
