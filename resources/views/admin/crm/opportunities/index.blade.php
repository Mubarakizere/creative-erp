<x-layouts.admin title="Opportunities">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Opportunities'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Opportunities</h1>
                <p class="mt-1 text-sm text-gray-500">Manage and track your deals pipeline.</p>
            </div>
            <div class="flex items-center gap-2">
                @can('viewAny', App\Models\Opportunity::class)
                    <x-button type="ghost" href="{{ route('admin.crm.opportunities.kanban') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        Kanban View
                    </x-button>
                @endcan
                @can('create', App\Models\Opportunity::class)
                    <x-button type="primary" href="{{ route('admin.crm.opportunities.create') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Create Opportunity
                    </x-button>
                @endcan
            </div>
        </div>
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.crm.opportunities.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-input name="search" placeholder="Search opportunities..." :value="request('search')" />
            <div class="flex items-end gap-2 sm:col-span-2 justify-end">
                <x-button type="primary" size="md">Filter</x-button>
                @if(request()->has('search'))
                    <x-button type="ghost" href="{{ route('admin.crm.opportunities.index') }}" size="md">Clear</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Account</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Value</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Probability</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-20">Actions</th>
        </x-slot:head>

        @forelse($opportunities as $opportunity)
            <tr @class(['bg-red-50/30' => $opportunity->trashed()])>
                <td class="px-4 py-3 font-semibold text-gray-900">{{ $opportunity->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $opportunity->account?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-gray-700">${{ number_format($opportunity->expected_revenue, 2) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $opportunity->probability }}%</td>
                <td class="px-4 py-3">
                    <x-badge type="primary">{{ $opportunity->status }}</x-badge>
                </td>
                <td class="px-4 py-3 text-right text-sm">
                    <a href="{{ route('admin.crm.opportunities.show', $opportunity) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-500">No opportunities found.</td>
            </tr>
        @endforelse

        <x-slot:pagination>{{ $opportunities->links('components.pagination') }}</x-slot:pagination>
    </x-table>
</x-layouts.admin>
