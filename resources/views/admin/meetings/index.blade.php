<x-layouts.admin title="Meetings">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Meetings']]" />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Meetings</h1>
                <p class="mt-1 text-sm text-gray-500">Manage and track all meetings across the organization.</p>
            </div>
            <a href="{{ route('admin.meetings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Meeting
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.meetings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <x-input name="search" placeholder="Search meetings..." :value="request('search')" />
            <x-select name="meeting_type">
                <option value="All">All Types</option>
                @foreach(\App\Models\Meeting::getMeetingTypes() as $key => $label)
                    <option value="{{ $key }}" {{ request('meeting_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </x-select>
            <x-select name="status">
                <option value="All">All Statuses</option>
                @foreach(\App\Models\Meeting::getStatuses() as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </x-select>
            <x-select name="project_id">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                @endforeach
            </x-select>
            <x-select name="sort">
                <option value="upcoming" {{ request('sort', 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="recently_updated" {{ request('sort') === 'recently_updated' ? 'selected' : '' }}>Recently Updated</option>
                <option value="meeting_type" {{ request('sort') === 'meeting_type' ? 'selected' : '' }}>Meeting Type</option>
            </x-select>
            <div class="flex gap-2">
                <x-button type="submit">Filter</x-button>
                <a href="{{ route('admin.meetings.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    {{-- Meetings List --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                                <div class="text-sm text-gray-900">{{ $meeting->start_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $meeting->start_at->format('g:i A') }} — {{ $meeting->end_at->format('g:i A') }}</div>
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
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.meetings.edit', $meeting) }}" class="text-amber-600 hover:text-amber-800" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button x-data @click="$dispatch('open-modal', 'delete-meeting-{{ $meeting->id }}')" type="button" class="text-red-600 hover:text-red-800" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
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
