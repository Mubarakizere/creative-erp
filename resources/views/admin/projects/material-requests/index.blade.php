<x-layouts.admin title="Project Material Requests">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Projects', 'url' => route('admin.projects.index')],
                ['label' => 'Material Requests'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\ProjectMaterialRequest::class)
    {{-- Top Hero Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/10 rounded-xl text-blue-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 14l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Project Material Requests</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Track and manage site material requisitions across all active projects.</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3 self-start sm:self-auto">
            @can('create', App\Models\ProjectMaterialRequest::class)
                <a href="{{ route('admin.material-requests.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Material Request
                </a>
            @endcan
        </div>
    </div>

    {{-- Stats Summary Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-stats-card 
            title="Total Requests" 
            :value="number_format($stats['total'] ?? 0)" 
            color="blue"
        >
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Pending Approvals" 
            :value="number_format($stats['pending'] ?? 0)" 
            color="amber"
        >
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Approved Requests" 
            :value="number_format($stats['approved'] ?? 0)" 
            color="emerald"
        >
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-stats-card>

        <x-stats-card 
            title="Draft Requests" 
            :value="number_format($stats['draft'] ?? 0)" 
            color="indigo"
        >
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </x-stats-card>
    </div>

    {{-- Filter & Search Bar --}}
    <x-card class="mb-6 p-3.5 sm:p-4 bg-white border border-gray-200/80 shadow-sm rounded-xl">
        <form method="GET" action="{{ route('admin.material-requests.index') }}" class="flex flex-col gap-3.5">
            {{-- Top Filter Row: Status Pills & Search --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                {{-- Status Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none max-w-full">
                    @php
                        $currentStatus = request('status', 'all');
                        $statuses = [
                            'all' => 'All Requests',
                            'Draft' => 'Draft',
                            'Submitted' => 'Submitted',
                            'Under Review' => 'Under Review',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                            'Cancelled' => 'Cancelled',
                        ];
                    @endphp
                    @foreach($statuses as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => 1]) }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap shrink-0 {{ $currentStatus === (string)$key ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Search & Clear Controls --}}
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-80">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by project name, code, request #, or user..." 
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    @if(request()->anyFilled(['search', 'status', 'project_id']))
                        <a href="{{ route('admin.material-requests.index') }}" 
                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors shrink-0"
                           title="Clear Filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Secondary Row: Project Filter Selector --}}
            <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs">
                <span class="font-medium text-gray-500 shrink-0">Filter by Project:</span>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto flex-1">
                    <select name="project_id" class="px-3 py-1.5 text-xs border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 flex-1 sm:max-w-xs">
                        <option value="">All Projects</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->code ? '[' . $proj->code . '] ' : '' }}{{ $proj->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3.5 py-1.5 bg-gray-900 text-white font-semibold rounded-md hover:bg-gray-800 transition-colors w-full sm:w-auto">
                        Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    {{-- Data Table Card --}}
    <x-card class="p-0 border border-gray-200/80 shadow-sm rounded-xl overflow-hidden mb-6">
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Project</th>
                        <th class="py-3.5 px-4">Request #</th>
                        <th class="py-3.5 px-4 text-center">Items</th>
                        <th class="py-3.5 px-4">Requested By</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Date</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-xs sm:text-sm">
                    @forelse($requests as $requestItem)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            {{-- COLUMN 1: PROJECT (FIRST) --}}
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        @if($requestItem->project?->code)
                                            <span class="font-mono text-[11px] font-bold text-blue-600 px-2 py-0.5 bg-blue-50 border border-blue-100 rounded shrink-0">
                                                {{ $requestItem->project->code }}
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.projects.show', $requestItem->project_id) }}" 
                                           class="font-semibold text-gray-900 hover:text-blue-600 hover:underline transition-colors text-xs truncate max-w-xs">
                                            {{ $requestItem->project->name ?? 'Unassigned Project' }}
                                        </a>
                                    </div>
                                    @if($requestItem->task)
                                        <span class="text-[10px] text-gray-400 mt-0.5">
                                            Task: {{ $requestItem->task->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- COLUMN 2: REQUEST NUMBER --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('admin.material-requests.show', $requestItem) }}" 
                                   class="font-mono text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $requestItem->request_number }}
                                </a>
                            </td>

                            {{-- COLUMN 3: ITEMS COUNT --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-gray-100 font-mono text-xs font-semibold text-gray-700 rounded-md">
                                    {{ $requestItem->items->count() }} {{ \Illuminate\Support\Str::plural('item', $requestItem->items->count()) }}
                                </span>
                            </td>

                            {{-- COLUMN 4: REQUESTED BY --}}
                            <td class="py-3.5 px-4 text-gray-600 text-xs whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ strtoupper(substr($requestItem->requestedBy->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span>{{ $requestItem->requestedBy->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            {{-- COLUMN 5: DATE --}}
                            <td class="py-3.5 px-4 text-gray-700 font-medium text-xs whitespace-nowrap">
                                {{ $requestItem->request_date ? $requestItem->request_date->format('M d, Y') : '-' }}
                            </td>

                            {{-- COLUMN 6: STATUS --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php
                                    $statusConfig = match($requestItem->status) {
                                        'Draft' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                                        'Submitted', 'Under Review' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-400'],
                                        'Approved' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                        'Rejected', 'Cancelled' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
                                        default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusConfig['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ $requestItem->status }}
                                </span>
                            </td>

                            {{-- COLUMN 7: ACTIONS --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-action-dropdown>
                                    @can('view', $requestItem)
                                        <x-action-dropdown-item href="{{ route('admin.material-requests.show', $requestItem) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('update', $requestItem)
                                        <x-action-dropdown-item href="{{ route('admin.material-requests.edit', $requestItem) }}" icon="edit">
                                            Edit Request
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('delete', $requestItem)
                                        <form method="POST" action="{{ route('admin.material-requests.destroy', $requestItem) }}" id="delete-request-form-{{ $requestItem->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this material request?')) document.getElementById('delete-request-form-{{ $requestItem->id }}').submit()" icon="delete" variant="danger">
                                            Delete Request
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center bg-gray-50/50">
                                <div class="max-w-xs mx-auto text-center px-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 14l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No material requests found</h3>
                                    <p class="mt-1 text-xs text-gray-500">No records match your selected project or search filter criteria.</p>
                                    @if(request()->anyFilled(['search', 'status', 'project_id']))
                                        <div class="mt-4">
                                            <a href="{{ route('admin.material-requests.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                                Clear all filters
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($requests, 'hasPages') && $requests->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        @endif
    </x-card>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view material requests.</p>
    </div>
    @endcan
</x-layouts.admin>
