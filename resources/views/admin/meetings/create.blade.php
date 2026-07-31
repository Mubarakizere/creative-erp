<x-layouts.admin title="Create Meeting">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Meetings', 'url' => route('admin.meetings.index')], ['label' => 'Create']]" />
        <h1 class="text-2xl font-bold text-gray-900 mt-2 tracking-tight">Schedule New Meeting</h1>
    </div>

    <form method="POST" action="{{ route('admin.meetings.store') }}" class="max-w-4xl">
        @csrf

        @if(isset($meetingableType) && isset($meetingableId))
            <input type="hidden" name="meetingable_type" value="{{ $meetingableType }}">
            <input type="hidden" name="meetingable_id" value="{{ $meetingableId }}">
        @elseif(old('meetingable_type') && old('meetingable_id'))
            <input type="hidden" name="meetingable_type" value="{{ old('meetingable_type') }}">
            <input type="hidden" name="meetingable_id" value="{{ old('meetingable_id') }}">
        @endif

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Meeting Details</h2>
            </div>
            <div class="p-6 space-y-6">
                <x-input name="title" label="Meeting Title" :value="old('title')" required placeholder="e.g., Weekly Team Standup" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-select name="company_id" label="Company" required placeholder="Select Company" :selected="old('company_id', auth()->user()->company_id)" :options="$companies->pluck('name', 'id')->toArray()" />

                    <x-select name="branch_id" label="Branch" required placeholder="Select Branch" :selected="old('branch_id', auth()->user()->branch_id)" :options="$branches->pluck('name', 'id')->toArray()" />

                    <x-select name="project_id" label="Project (Optional)" placeholder="No Project" :selected="old('project_id', $selectedProject?->id)" :options="$projects->pluck('name', 'id')->toArray()" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-select name="meeting_type" label="Meeting Type" required :placeholder="false" :selected="old('meeting_type', 'internal')" :options="\App\Models\Meeting::getMeetingTypes()" />

                    <x-select name="timezone" label="Timezone" required :placeholder="false" :selected="old('timezone', 'UTC')" :options="array_combine(timezone_identifiers_list(), timezone_identifiers_list())" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input type="datetime-local" name="start_at" label="Start Date & Time" :value="old('start_at')" required />
                    <x-input type="datetime-local" name="end_at" label="End Date & Time" :value="old('end_at')" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="location" label="Location" :value="old('location')" placeholder="e.g., Conference Room A" />
                    <x-input name="meeting_link" label="Meeting Link" :value="old('meeting_link')" placeholder="https://meet.google.com/..." />
                </div>

                <x-textarea name="description" label="Description" :value="old('description')" rows="3" placeholder="Meeting agenda and objectives..." />

                <x-textarea name="notes" label="Meeting Notes" :value="old('notes')" rows="3" placeholder="Notes, action items..." />
            </div>
        </div>

        {{-- Attendees --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm mt-6 overflow-hidden" x-data="{ search: '' }">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Attendees</h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Select users to invite to this meeting.</p>
                </div>
                <div class="relative max-w-xs w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input x-model="search" type="text" placeholder="Search attendees..." class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-3 py-2 text-sm shadow-sm transition-colors duration-200">
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-1">
                    @foreach($users as $user)
                        <label x-show="search === '' || '{{ strtolower($user->full_name . ' ' . ($user->job_title ?? $user->email)) }}'.includes(search.toLowerCase())" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer" x-transition>
                            <input type="checkbox" name="attendees[]" value="{{ $user->id }}"
                                {{ in_array($user->id, old('attendees', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600 flex-shrink-0">
                                    {{ $user->initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->job_title ?? $user->email }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div x-show="search !== '' && !Array.from($el.previousElementSibling.children).some(el => el.style.display !== 'none')" class="text-center py-4 text-sm text-gray-500" style="display: none;">
                    No attendees found matching your search.
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 mt-6">
            <x-button submit="true">Schedule Meeting</x-button>
            <a href="{{ route('admin.meetings.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
