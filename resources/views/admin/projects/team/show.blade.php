<x-layouts.admin title="Team Member Profile">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => $teamMember->project->name, 'url' => route('admin.projects.show', $teamMember->project_id)],
                ['label' => 'Team', 'url' => route('admin.projects.team.index')],
                ['label' => $teamMember->user->full_name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Member Profile</h1>
        </div>
        <div class="flex items-center gap-3">
            <x-button type="default" href="{{ route('admin.projects.show', $teamMember->project_id) }}">
                Back to Project
            </x-button>
            @if(!$teamMember->trashed())
                @can('update', $teamMember)
                    <x-button type="primary" href="{{ route('admin.projects.team.edit', $teamMember) }}">
                        Edit Assignment
                    </x-button>
                @endcan
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
            <x-card class="text-center">
                <div class="flex justify-center mb-4">
                    @if($teamMember->user->avatar)
                        <img class="h-24 w-24 rounded-full border-4 border-white shadow" src="{{ asset('storage/' . $teamMember->user->avatar) }}" alt="">
                    @else
                        <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-3xl border-4 border-white shadow">
                            {{ $teamMember->user->initials }}
                        </div>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $teamMember->user->full_name }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $teamMember->user->job_title ?? 'Employee' }}</p>
                
                <div class="mb-4">
                    @if($teamMember->trashed())
                        <x-badge type="danger">Removed</x-badge>
                    @else
                        <x-badge :type="$teamMember->status === 'Active' ? 'success' : 'default'">
                            {{ $teamMember->status }}
                        </x-badge>
                    @endif
                </div>
                
                <div class="border-t pt-4 text-left">
                    <div class="mb-2">
                        <span class="text-xs font-medium text-gray-500 uppercase">Email</span>
                        <p class="text-sm text-gray-900">{{ $teamMember->user->email }}</p>
                    </div>
                    <div class="mb-2">
                        <span class="text-xs font-medium text-gray-500 uppercase">Phone</span>
                        <p class="text-sm text-gray-900">{{ $teamMember->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">System Status</span>
                        <p class="text-sm text-gray-900 capitalize">{{ $teamMember->user->status }}</p>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="md:col-span-2 space-y-6">
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Project Assignment Details</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Project</span>
                        <p class="mt-1 text-sm font-medium text-blue-600">
                            <a href="{{ route('admin.projects.show', $teamMember->project_id) }}">{{ $teamMember->project->name }}</a>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Department</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $teamMember->department->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Project Role</span>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $teamMember->project_role }}
                            @if($teamMember->project_role === 'Project Manager')
                                <svg class="w-4 h-4 text-amber-500 inline ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Allocation Percentage</span>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden max-w-[150px]">
                                <div class="h-full bg-blue-600 rounded-full" style="width: {{ $teamMember->allocation_percentage }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $teamMember->allocation_percentage }}%</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Hourly Rate</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $teamMember->hourly_rate ? format_currency($teamMember->hourly_rate, $teamMember->project->currency) : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Joined Date</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $teamMember->joined_at->format('M j, Y') }}</p>
                    </div>
                    @if($teamMember->left_at)
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase">Left Date</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $teamMember->left_at->format('M j, Y') }}</p>
                    </div>
                    @endif
                    <div class="sm:col-span-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Notes</span>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-gray-50 p-3 rounded-lg">{{ $teamMember->notes ?: 'No notes provided.' }}</p>
                    </div>
                </div>
            </x-card>
            
            {{-- Audit Details --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">Audit Details</h3>
                <ul class="space-y-3">
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Assigned At:</span>
                        <span class="text-gray-900 ml-1">{{ $teamMember->created_at?->format('Y-m-d H:i') }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Assigned By:</span>
                        <span class="text-gray-900 ml-1">{{ $teamMember->creator?->name ?? 'System' }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Last Updated:</span>
                        <span class="text-gray-900 ml-1">{{ $teamMember->updated_at?->format('Y-m-d H:i') }}</span>
                    </li>
                    <li class="text-sm">
                        <span class="font-semibold text-gray-500">Updated By:</span>
                        <span class="text-gray-900 ml-1">{{ $teamMember->updater?->name ?? 'System' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
