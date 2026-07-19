<x-layouts.admin title="Opportunity Details">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => $opportunity->name]]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $opportunity->name }}</h1>
        </div>
        <div class="flex items-center gap-2">
            @can('update', $opportunity)
                <x-button type="ghost" href="{{ route('admin.crm.opportunities.edit', $opportunity) }}">Edit</x-button>
            @endcan
        </div>
    </div>

    <x-card>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500 block">Name</span><span class="font-medium text-gray-900">{{ $opportunity->name }}</span></div>
            <div><span class="text-gray-500 block">Expected Revenue</span><span class="font-medium text-gray-900">${{ number_format($opportunity->expected_revenue, 2) }}</span></div>
            <div><span class="text-gray-500 block">Probability</span><span class="font-medium text-gray-900">{{ $opportunity->probability }}%</span></div>
            <div><span class="text-gray-500 block">Status</span><span class="font-medium text-gray-900">{{ $opportunity->status }}</span></div>
        </div>
    </x-card>
</x-layouts.admin>
