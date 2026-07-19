<x-layouts.admin title="Pipelines">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Pipelines</h1></div>
        @can('create', App\Models\Pipeline::class)
            <x-button type="primary" href="{{ route('admin.crm.pipelines.create') }}">Create Pipeline</x-button>
        @endcan
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Company</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-20">Actions</th>
        </x-slot:head>

        @forelse($pipelines as $pipeline)
            <tr>
                <td class="px-4 py-3 font-semibold text-gray-900">{{ $pipeline->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $pipeline->company?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-right text-sm">
                    <a href="{{ route('admin.crm.pipelines.show', $pipeline) }}" class="text-blue-600 hover:underline">View</a>
                    <a href="{{ route('admin.crm.pipelines.edit', $pipeline) }}" class="text-blue-600 hover:underline ml-2">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-12 text-center text-gray-500">No pipelines found.</td></tr>
        @endforelse
    </x-table>
</x-layouts.admin>
