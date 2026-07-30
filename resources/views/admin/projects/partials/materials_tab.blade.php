<x-card>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Project Material Issues</h3>
        @can('material_issue.create')
            <x-button type="primary" href="{{ route('admin.project-material-issues.create') }}?project_id={{ $project->id }}" size="sm">
                Issue Material
            </x-button>
        @endcan
    </div>
    
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Total Material Issues</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $project->materialIssues()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="truncate text-sm font-medium text-gray-500">Actual Material Cost</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ format_currency($project->actual_cost, $project->currency) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Issue Number</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Issued By</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cost</th>
        </x-slot:head>

        @forelse($project->materialIssues()->with(['warehouse', 'issuer', 'items'])->latest()->get() as $issue)
            <tr>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.project-material-issues.show', $issue) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                        {{ $issue->issue_number }}
                    </a>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600">{{ $issue->issue_date->format('M d, Y') }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600">{{ $issue->warehouse->name }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-gray-600">{{ $issue->issuer->name }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm font-medium text-gray-900">{{ number_format($issue->items->sum('total_cost'), 2) }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No materials issued to this project yet.</td>
            </tr>
        @endforelse
    </x-table>
</x-card>
