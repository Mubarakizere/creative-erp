<?php

$modules = [
    'bins' => ['model' => 'WarehouseBin', 'title' => 'Warehouse Bins', 'route' => 'warehouse.bins'],
    'tasks' => ['model' => 'WarehouseTask', 'title' => 'Warehouse Tasks', 'route' => 'warehouse.tasks'],
    'put-aways' => ['model' => 'WarehouseTask', 'title' => 'Put Aways', 'route' => 'warehouse.put-aways'], // Actually PutAway uses WarehouseTask but controller uses put-aways
    'pickings' => ['model' => 'WarehousePicking', 'title' => 'Pickings', 'route' => 'warehouse.pickings'],
    'packings' => ['model' => 'WarehousePacking', 'title' => 'Packings', 'route' => 'warehouse.packings'],
    'shipments' => ['model' => 'WarehouseShipment', 'title' => 'Shipments', 'route' => 'warehouse.shipments'],
    'movements' => ['model' => 'WarehouseMovement', 'title' => 'Movements', 'route' => 'warehouse.movements'],
    'returns' => ['model' => 'WarehouseReturn', 'title' => 'Returns', 'route' => 'warehouse.returns'],
    'cycle-counts' => ['model' => 'WarehouseCycleCount', 'title' => 'Cycle Counts', 'route' => 'warehouse.cycle-counts'],
];

foreach ($modules as $dir => $config) {
    $path = "c:/Users/mouba/creative-erp/resources/views/admin/warehouse/{$dir}";
    if (!is_dir($path)) mkdir($path, 0755, true);
    
    // Index
    $index = <<<EOT
<x-layouts.admin title="{$config['title']}">
    <x-slot:breadcrumbs>
        @php
            \$breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => '{$config['title']}'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{$config['title']}</h1>
            <p class="mt-1 text-sm text-gray-500">Manage {$config['title']}.</p>
        </div>
        @can('create', App\Models\\{$config['model']}::class)
            <x-button type="primary" href="{{ route('admin.{$config['route']}.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create New
            </x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference/ID</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse(\$items as \$item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-900">{{ \$item->reference_number ?? \$item->id }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-gray-600/20">
                                    {{ ucfirst(\$item->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.{$config['route']}.show', \$item) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">View</a>
                                    <a href="{{ route('admin.{$config['route']}.edit', \$item) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists(\$items, 'links'))
                {{ \$items->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>
EOT;
    file_put_contents("{$path}/index.blade.php", $index);

    // Show
    $show = <<<EOT
<x-layouts.admin title="View {$config['title']}">
    <x-slot:breadcrumbs>
        @php
            \$breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => '{$config['title']}', 'url' => route('admin.{$config['route']}.index')],
                ['label' => 'View'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">View {$config['title']}</h1>
        </div>
        <x-button href="{{ route('admin.{$config['route']}.index') }}">
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
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ \$item->reference_number ?? \$item->id }}</dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium text-gray-900">Status</dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ ucfirst(\$item->status ?? 'pending') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </x-card>
</x-layouts.admin>
EOT;
    file_put_contents("{$path}/show.blade.php", $show);
    
    // Create
    $create = <<<EOT
<x-layouts.admin title="Create {$config['title']}">
    <x-slot:breadcrumbs>
        @php
            \$breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => '{$config['title']}', 'url' => route('admin.{$config['route']}.index')],
                ['label' => 'Create'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create {$config['title']}</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.{$config['route']}.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Example Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="button" href="{{ route('admin.{$config['route']}.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Save</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
EOT;
    file_put_contents("{$path}/create.blade.php", $create);

    // Edit
    $edit = <<<EOT
<x-layouts.admin title="Edit {$config['title']}">
    <x-slot:breadcrumbs>
        @php
            \$breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => '{$config['title']}', 'url' => route('admin.{$config['route']}.index')],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit {$config['title']}</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.{$config['route']}.update', \$item) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Example Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="pending" {{ (\$item->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ (\$item->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button type="button" href="{{ route('admin.{$config['route']}.index') }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Update</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
EOT;
    file_put_contents("{$path}/edit.blade.php", $edit);
}

echo "WMS Blades generated successfully!\n";
