<x-layouts.admin title="Pipeline Details">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => $pipeline->name]]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">{{ $pipeline->name }}</h1></div>
        <div class="flex items-center gap-2">
            @can('update', $pipeline)
                <x-button type="ghost" href="{{ route('admin.crm.pipelines.edit', $pipeline) }}">Edit</x-button>
            @endcan
        </div>
    </div>

    <x-card>
        <div class="grid grid-cols-1 gap-4 text-sm">
            <div><span class="text-gray-500 block">Name</span><span class="font-medium text-gray-900">{{ $pipeline->name }}</span></div>
            <div><span class="text-gray-500 block">Company</span><span class="font-medium text-gray-900">{{ $pipeline->company?->name ?? 'N/A' }}</span></div>
        </div>
    </x-card>
</x-layouts.admin>
