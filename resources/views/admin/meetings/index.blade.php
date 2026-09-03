<x-layouts.admin title="Meetings">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Meetings']]" />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Meetings</h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">Manage and track all meetings across the organization.</p>
            </div>
            @can('create', App\Models\Meeting::class)
            <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Meeting
            </a>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('admin.meetings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <x-input name="search" placeholder="Search meetings..." :value="request('search')" />
            <x-select name="meeting_type" placeholder="All Types" :selected="request('meeting_type')" :options="\App\Models\Meeting::getMeetingTypes()" />
            
            <x-select name="status" placeholder="All Statuses" :selected="request('status')" :options="\App\Models\Meeting::getStatuses()" />
            
            <x-select name="project_id" placeholder="All Projects" :selected="request('project_id')" :options="$projects->pluck('name', 'id')->toArray()" />
            
            <x-select name="sort" :placeholder="false" :selected="request('sort', 'upcoming')" :options="[
                'upcoming' => 'Upcoming',
                'newest' => 'Newest',
                'oldest' => 'Oldest',
                'recently_updated' => 'Recently Updated',
                'meeting_type' => 'Meeting Type'
            ]" />
            <div class="flex gap-2">
                <x-button type="submit">Filter</x-button>
                <a href="{{ route('admin.meetings.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    {{-- Meetings List --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Meeting</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Type</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Date & Time</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Attendees</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">{{ $meeting->title }}</a>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $meeting->project?->name ?? 'No Project' }}
                                        @if($meeting->location) • {{ $meeting->location }} @endif
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                    {{ match($meeting->meeting_type) {
                                        'internal' => 'bg-blue-100 text-blue-800',
                                        'client' => 'bg-purple-100 text-purple-800',
                                        'project' => 'bg-cyan-100 text-cyan-800',
                                        'hr' => 'bg-amber-100 text-amber-800',
                                        'training' => 'bg-emerald-100 text-emerald-800',
                                        'sales' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    } }}">
                                    {{ \App\Models\Meeting::getMeetingTypes()[$meeting->meeting_type] ?? $meeting->meeting_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $meeting->start_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $meeting->start_at->format('g:i A') }} to {{ $meeting->end_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    @foreach($meeting->attendees->take(4) as $attendee)
                                        <div class="w-7 h-7 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-blue-600" title="{{ $attendee->full_name }}">
                                            {{ $attendee->initials }}
                                        </div>
                                    @endforeach
                                    @if($meeting->attendees->count() > 4)
                                        <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[10px] font-bold text-gray-600">+{{ $meeting->attendees->count() - 4 }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :type="match($meeting->status) {
                                    'scheduled' => 'primary',
                                    'in_progress' => 'warning',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'rescheduled' => 'default',
                                    default => 'default',
                                }">
                                    {{ \App\Models\Meeting::getStatuses()[$meeting->status] ?? $meeting->status }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-action-dropdown>
                                    @can('view', $meeting)
                                        <x-action-dropdown-item href="{{ route('admin.meetings.show', $meeting) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan
                                    @can('update', $meeting)
                                        <x-action-dropdown-item href="{{ route('admin.meetings.edit', $meeting) }}" icon="edit">
                                            Edit Meeting
                                        </x-action-dropdown-item>
                                    @endcan
                                    @can('delete', $meeting)
                                        <x-action-dropdown-item @click="$dispatch('open-modal', 'delete-meeting-{{ $meeting->id }}')" icon="delete" variant="danger">
                                            Delete Meeting
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>

                        {{-- Delete Modal --}}
                        <x-modal id="delete-meeting-{{ $meeting->id }}" maxWidth="md">
                            <x-slot:header>Cancel / Delete Meeting</x-slot:header>
        
                            <div class="text-center py-4">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete "{{ $meeting->title }}"?</h3>
                                <p class="text-sm text-gray-500">This action will soft-delete the meeting and notify the attendees.</p>
                            </div>
        
                            <x-slot:footer>
                                <x-button type="ghost" @click="show = false">Cancel</x-button>
                                <form method="POST" action="{{ route('admin.meetings.destroy', $meeting) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="danger" submit="true">Delete Meeting</x-button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-gray-500 text-sm">No meetings found.</p>
                                <a href="{{ route('admin.meetings.create') }}" class="text-blue-600 text-sm hover:text-blue-700 font-medium">Create your first meeting</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($meetings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $meetings->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
