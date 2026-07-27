<x-layouts.admin title="Edit Movement">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Movements', 'url' => route('admin.warehouse.movements.index')],
                ['label' => $movement->movement_number, 'url' => route('admin.warehouse.movements.show', $movement)],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Movement: {{ $movement->movement_number }}</h1>
        <p class="mt-1 text-sm text-gray-500">Note: Core movement details (locations and quantities) cannot be changed once requested. Cancel and recreate if needed.</p>
    </div>

    <x-card>
        <div class="p-6">
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-700">Currently, only the status can be modified via workflow actions on the <a href="{{ route('admin.warehouse.movements.show', $movement) }}" class="text-blue-600 hover:underline">View page</a>.</p>
            </div>
            
            <x-button type="button" href="{{ route('admin.warehouse.movements.show', $movement) }}">Back to Details</x-button>
        </div>
    </x-card>
</x-layouts.admin>