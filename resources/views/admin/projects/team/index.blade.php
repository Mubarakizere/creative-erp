<x-layouts.admin title="Project Teams">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Project Teams'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Project Teams</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all team members across all projects.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('project-team.assign')
                <x-button type="primary" href="{{ route('admin.projects.team.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Assign Member
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <x-card class="mb-6">
        <form action="{{ route('admin.projects.team.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input type="text" name="search" label="Search" placeholder="Name or Project..." value="{{ request('search') }}" />
            </div>
            <div>
                <x-select name="project_id" label="Project" :options="$projects->pluck('name', 'id')->toArray()" :selected="request('project_id')" placeholder="All Projects" />
            </div>
            <div>
                <x-select name="department_id" label="Department" :options="$departments->pluck('name', 'id')->toArray()" :selected="request('department_id')" placeholder="All Departments" />
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-select name="status" label="Status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" :selected="request('status')" placeholder="All Statuses" />
                </div>
                <div class="pb-1">
                    <x-button type="primary" submit>Filter</x-button>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Team Members Table --}}
    <x-card>
        <x-table>
            <x-slot:head>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Dept</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocation</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </x-slot:head>
            
            @forelse($members as $member)
                <tr class="{{ $member->trashed() ? 'bg-gray-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($member->user->avatar)
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $member->user->avatar) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                        {{ $member->user->initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $member->user->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $member->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.projects.show', $member->project_id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            {{ $member->project->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $member->project_role }}</div>
                        <div class="text-sm text-gray-500">{{ $member->department->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->allocation_percentage }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($member->trashed())
                            <x-badge type="danger">Removed</x-badge>
                        @else
                            <x-badge :type="$member->status === 'Active' ? 'success' : 'default'">
                                {{ $member->status }}
                            </x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            @can('view', $member)
                                <a href="{{ route('admin.projects.team.show', $member) }}" class="text-blue-600 hover:text-blue-900" title="View Profile">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            @endcan
                            
                            @if(!$member->trashed())
                                @can('update', $member)
                                    <a href="{{ route('admin.projects.team.edit', $member) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit Assignment">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No team members found.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $members->links('components.pagination') }}
        </div>
    </x-card>
</x-layouts.admin>
