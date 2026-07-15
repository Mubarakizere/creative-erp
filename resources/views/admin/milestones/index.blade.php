<x-layouts.admin title="Milestones">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Milestones'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Milestones</h1>
                <p class="mt-1 text-sm text-gray-500">Manage milestones across all projects.</p>
            </div>
            @can('create', App\Models\Milestone::class)
                <x-button type="primary" href="{{ route('admin.milestones.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Milestone
                </x-button>
            @endcan
        </div>
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.milestones.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-input name="search" placeholder="Search milestones..." :value="request('search')" />
            
            <x-select name="project_id" placeholder="All Projects" :options="$projects->pluck('name', 'id')->toArray()" :selected="request('project_id')" />
            
            <x-select name="status" placeholder="All Statuses" :options="['Pending' => 'Pending', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'On Hold' => 'On Hold']" :selected="request('status')" />

            <div class="flex items-end gap-2">
                <x-button type="primary" size="md">Filter</x-button>
                @if(request()->hasAny(['search', 'status', 'project_id']))
                    <x-button type="ghost" href="{{ route('admin.milestones.index') }}" size="md">Clear</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Milestone</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Project</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Due Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Progress</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($milestones as $milestone)
            <tr @class(['bg-red-50/30' => $milestone->trashed()])>
                <td class="px-4 py-3">
                    <div>
                        <a href="{{ route('admin.milestones.show', $milestone) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $milestone->name }}
                        </a>
                    </div>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <a href="{{ route('admin.projects.show', $milestone->project) }}" class="text-sm text-gray-600 hover:text-blue-600">
                        {{ $milestone->project?->name }}
                    </a>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="text-sm text-gray-600">{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : '-' }}</span>
                </td>
                <td class="px-4 py-3">
                    @php
                        $statusType = match($milestone->status) {
                            'Pending' => 'warning',
                            'In Progress' => 'primary',
                            'Completed' => 'success',
                            'On Hold' => 'danger',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ $milestone->status }}</x-badge>
                    @if($milestone->trashed())
                        <x-badge type="danger" class="ml-1">Archived</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden w-24">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $milestone->progress }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-600">{{ $milestone->progress }}%</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-right">
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                        </button>
                        <div x-show="open" class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1" style="display: none;">
                            @can('view', $milestone)
                                <a href="{{ route('admin.milestones.show', $milestone) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View</a>
                            @endcan
                            @if(!$milestone->trashed())
                                @can('update', $milestone)
                                    <a href="{{ route('admin.milestones.edit', $milestone) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Edit</a>
                                @endcan
                                @can('delete', $milestone)
                                    <form method="POST" action="{{ route('admin.milestones.destroy', $milestone) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Archive</button>
                                    </form>
                                @endcan
                            @else
                                @can('restore', $milestone)
                                    <form method="POST" action="{{ route('admin.milestones.restore', $milestone) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">Restore</button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No milestones found.</td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $milestones->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
