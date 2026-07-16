<x-layouts.admin title="{{ $meeting->title }}">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Meetings', 'url' => route('admin.meetings.index')], ['label' => $meeting->title]]" />
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $meeting->title }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    <x-badge :type="match($meeting->status) { 'scheduled' => 'primary', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger', default => 'default' }">
                        {{ \App\Models\Meeting::getStatuses()[$meeting->status] ?? $meeting->status }}
                    </x-badge>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                        {{ match($meeting->meeting_type) { 'internal' => 'bg-blue-100 text-blue-800', 'client' => 'bg-purple-100 text-purple-800', 'project' => 'bg-cyan-100 text-cyan-800', default => 'bg-gray-100 text-gray-800' } }}">
                        {{ \App\Models\Meeting::getMeetingTypes()[$meeting->meeting_type] ?? $meeting->meeting_type }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $meeting->formatted_duration }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($meeting->status !== 'cancelled')
                    <a href="{{ route('admin.meetings.edit', $meeting) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.meetings.cancel', $meeting) }}" onsubmit="return confirm('Cancel this meeting?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Meeting
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Meeting Details</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meeting->start_at->format('l, F j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meeting->start_at->format('g:i A') }} — {{ $meeting->end_at->format('g:i A') }} ({{ $meeting->timezone }})</dd>
                        </div>
                        @if($meeting->location)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meeting->location }}</dd>
                            </div>
                        @endif
                        @if($meeting->meeting_link)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Meeting Link</dt>
                                <dd class="mt-1"><a href="{{ $meeting->meeting_link }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700 underline break-all">Join Meeting</a></dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meeting->company->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Branch</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meeting->branch->name }}</dd>
                        </div>
                        @if($meeting->project)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Project</dt>
                                <dd class="mt-1"><a href="{{ route('admin.projects.show', $meeting->project) }}" class="text-sm text-blue-600 hover:text-blue-700">{{ $meeting->project->name }}</a></dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Organized By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $meeting->creator->full_name }}</dd>
                        </div>
                    </dl>

                    @if($meeting->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $meeting->description }}</p>
                        </div>
                    @endif

                    @if($meeting->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Meeting Notes</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $meeting->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Your Response --}}
            @if($userAttendance)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Your Response</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-3">
                            Status: <span class="font-medium capitalize">{{ $userAttendance->pivot->attendance_status }}</span>
                        </p>
                        @if($meeting->status !== 'cancelled')
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.meetings.respond', $meeting) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="response" value="accepted">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg {{ $userAttendance->pivot->attendance_status === 'accepted' ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-500' : 'bg-gray-100 text-gray-700 hover:bg-emerald-50' }} transition-colors">✓ Accept</button>
                                </form>
                                <form method="POST" action="{{ route('admin.meetings.respond', $meeting) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="response" value="tentative">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg {{ $userAttendance->pivot->attendance_status === 'tentative' ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-500' : 'bg-gray-100 text-gray-700 hover:bg-amber-50' }} transition-colors">? Tentative</button>
                                </form>
                                <form method="POST" action="{{ route('admin.meetings.respond', $meeting) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="response" value="declined">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg {{ $userAttendance->pivot->attendance_status === 'declined' ? 'bg-red-100 text-red-800 ring-1 ring-red-500' : 'bg-gray-100 text-gray-700 hover:bg-red-50' }} transition-colors">✕ Decline</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Attendees --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Attendees ({{ $meeting->attendees->count() }})</h3>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($meeting->attendees as $attendee)
                        <div class="flex items-center justify-between gap-2 p-2 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600 flex-shrink-0">
                                    {{ $attendee->initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attendee->full_name }}</p>
                                    @if($attendee->id === $meeting->created_by)
                                        <p class="text-xs text-blue-600">Organizer</p>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0
                                {{ match($attendee->pivot->attendance_status) {
                                    'accepted' => 'bg-emerald-100 text-emerald-800',
                                    'declined' => 'bg-red-100 text-red-800',
                                    'tentative' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst($attendee->pivot->attendance_status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Information</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Created</span><span class="text-gray-900">{{ $meeting->created_at->format('M j, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Updated</span><span class="text-gray-900">{{ $meeting->updated_at->diffForHumans() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Duration</span><span class="text-gray-900">{{ $meeting->formatted_duration }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
