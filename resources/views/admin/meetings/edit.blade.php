<x-layouts.admin title="Edit Meeting">
    <div class="mb-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => 'Meetings', 'url' => route('admin.meetings.index')], ['label' => 'Edit']]" />
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Meeting</h1>
    </div>

    <form method="POST" action="{{ route('admin.meetings.update', $meeting) }}" class="max-w-4xl">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Meeting Details</h2>
            </div>
            <div class="p-6 space-y-6">
                <x-input name="title" label="Meeting Title" :value="old('title', $meeting->title)" required />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-select name="company_id" label="Company" required>
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $meeting->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="branch_id" label="Branch" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $meeting->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="project_id" label="Project (Optional)">
                        <option value="">No Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $meeting->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-select name="meeting_type" label="Meeting Type" required>
                        @foreach(\App\Models\Meeting::getMeetingTypes() as $key => $label)
                            <option value="{{ $key }}" {{ old('meeting_type', $meeting->meeting_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="status" label="Status">
                        @foreach(\App\Models\Meeting::getStatuses() as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $meeting->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="timezone" label="Timezone" required>
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ old('timezone', $meeting->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input type="datetime-local" name="start_at" label="Start Date & Time" :value="old('start_at', $meeting->start_at->format('Y-m-d\TH:i'))" required />
                    <x-input type="datetime-local" name="end_at" label="End Date & Time" :value="old('end_at', $meeting->end_at->format('Y-m-d\TH:i'))" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="location" label="Location" :value="old('location', $meeting->location)" />
                    <x-input name="meeting_link" label="Meeting Link" :value="old('meeting_link', $meeting->meeting_link)" />
                </div>

                <x-textarea name="description" label="Description" :value="old('description', $meeting->description)" rows="3" />
                <x-textarea name="notes" label="Meeting Notes" :value="old('notes', $meeting->notes)" rows="3" />
            </div>
        </div>

        {{-- Attendees --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-6" x-data="{ search: '' }">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Attendees</h2>
                    <p class="text-sm text-gray-500 mt-1">Select users to invite to this meeting.</p>
                </div>
                <div class="relative max-w-xs w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input x-model="search" type="text" placeholder="Search attendees..." class="block w-full rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-3 py-2 text-sm shadow-sm transition-colors duration-200">
                </div>
            </div>
            <div class="p-6">
                @php $currentAttendeeIds = old('attendees', $meeting->attendees->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-1">
                    @foreach($users as $user)
                        <label x-show="search === '' || '{{ strtolower($user->full_name . ' ' . ($user->job_title ?? $user->email)) }}'.includes(search.toLowerCase())" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer" x-transition>
                            <input type="checkbox" name="attendees[]" value="{{ $user->id }}"
                                {{ in_array($user->id, $currentAttendeeIds) ? 'checked' : '' }}
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
            <x-button submit="true">Update Meeting</x-button>
            <a href="{{ route('admin.meetings.show', $meeting) }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
