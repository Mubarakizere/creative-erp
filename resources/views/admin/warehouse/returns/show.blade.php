<x-layouts.admin title="View Returns">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Returns', 'url' => route('admin.warehouse.returns.index')],
                ['label' => 'View'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">View Returns</h1>
        </div>
        <x-button href="{{ route('admin.warehouse.returns.index') }}">
            Back to List
        </x-button>
    </div>

    <x-card>
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Details</h3>
            <div class="mt-4 border-t border-gray-100">
                <dl class="divide-y divide-gray-100">
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium text-gray-900">ID / Reference</dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $item->reference_number ?? $item->id }}</dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium text-gray-900">Status</dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ ucfirst($item->status ?? 'pending') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </x-card>
</x-layouts.admin>