<x-layouts.admin title="My Timesheet">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Time Tracking', 'url' => route('admin.time-tracking.index')],
                ['label' => 'My Timesheet'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Timesheet</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">View and manage your tracked time.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="bg-gray-100/80 p-1.5 rounded-xl flex items-center text-sm font-medium border border-gray-200/50">
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'daily']) }}" 
                   @class(['px-4 py-1.5 rounded-lg transition-all', 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5' => $period === 'daily', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50' => $period !== 'daily'])>
                    Daily
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'weekly']) }}" 
                   @class(['px-4 py-1.5 rounded-lg transition-all', 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5' => $period === 'weekly', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50' => $period !== 'weekly'])>
                    Weekly
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'monthly']) }}" 
                   @class(['px-4 py-1.5 rounded-lg transition-all', 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5' => $period === 'monthly', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50' => $period !== 'monthly'])>
                    Monthly
                </a>
                <a href="{{ route('admin.time-tracking.timesheet', ['period' => 'all']) }}" 
                   @class(['px-4 py-1.5 rounded-lg transition-all', 'bg-white shadow-sm text-gray-900 ring-1 ring-black/5' => $period === 'all', 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50' => $period !== 'all'])>
                    All Time
                </a>
            </div>
            
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2 text-right shadow-sm">
                <span class="block text-[11px] font-bold text-blue-600 tracking-widest uppercase">Total Logged</span>
                <span class="block text-lg font-black text-blue-900 tracking-tight">
                    {{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m
                </span>
            </div>

            @can('create', App\Models\TimeEntry::class)
            <button type="button" x-data @click="$dispatch('open-modal', 'create-time-entry')" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Log Time
            </button>
            @endcan
        </div>
    </div>

    <!-- Stats summary could go here (hours today, week, month) -->
    
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Project / Task</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Date & Time</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Duration</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Billable</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $entry->project->name }}</p>
                                @if($entry->task)
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $entry->task->title }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $entry->start_time->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $entry->start_time->format('g:i A') }} to {{ $entry->end_time->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">{{ intdiv($entry->duration_minutes, 60) }}h {{ $entry->duration_minutes % 60 }}m</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($entry->billable)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Billable</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">Non-Billable</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-action-dropdown>
                                    @can('delete', $entry)
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-time-entry-{{ $entry->id }}')" icon="delete" variant="danger">
                                            Delete Log
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>

                        {{-- Delete Modal --}}
                        <x-modal id="delete-time-entry-{{ $entry->id }}" maxWidth="md">
                            <x-slot:header>Delete Time Entry</x-slot:header>
                            <div class="text-center py-4">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Delete Time Entry?</h3>
                                <p class="text-sm text-gray-500 font-medium">Are you sure you want to delete this time entry? This action cannot be undone.</p>
                            </div>
                            <x-slot:footer>
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                <form method="POST" action="{{ route('admin.time-tracking.destroy', $entry) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Entry</button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No time entries found</h3>
                                <p class="text-sm text-gray-500">You haven't logged any time for this period.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    <x-modal id="create-time-entry" maxWidth="md">
        <x-slot:header>Log Time</x-slot:header>
        <form id="create-time-entry-form" method="POST" action="{{ route('admin.time-tracking.store') }}">
            @csrf
            <div class="space-y-4 py-2 px-1">
                <x-project-task-search :projects="$projects" />
                <div class="grid grid-cols-2 gap-4">
                    <x-input type="datetime-local" name="start_time" label="Start Time" required />
                    <x-input type="datetime-local" name="end_time" label="End Time" required />
                </div>
                <x-textarea name="description" label="Description" rows="2" placeholder="What did you work on?" />
                
                <div class="bg-blue-50/50 rounded-xl p-3 flex items-center justify-between border border-blue-100/50">
                    <div>
                        <p class="text-sm font-bold text-gray-900 tracking-tight">Billable Time</p>
                        <p class="text-xs text-gray-500 font-medium">Check if this time is billable to the client.</p>
                    </div>
                    <div class="flex items-center">
                        <input type="hidden" name="billable" value="0">
                        <input type="checkbox" name="billable" id="billable" value="1" checked class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                    </div>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="create-time-entry-form" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Save Entry</button>
        </x-slot:footer>
    </x-modal>
</x-layouts.admin>
