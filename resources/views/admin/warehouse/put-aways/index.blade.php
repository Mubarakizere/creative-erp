<x-layouts.admin title="Put Aways">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Put Aways'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Put Aways</h1>
            <p class="mt-1 text-sm text-gray-500">Manage Put Away tasks for incoming goods.</p>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Task ID</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">#{{ $item->id }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $item->taskable->product->name ?? 'N/A' }}
                                <div class="text-xs text-gray-500">{{ $item->taskable->product->sku ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ number_format($item->taskable->quantity_received ?? 0, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                <span @class([
                                    'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ring-1 ring-inset',
                                    'bg-yellow-50 text-yellow-700 ring-yellow-600/20' => $item->status === 'pending',
                                    'bg-green-50 text-green-700 ring-green-600/20' => $item->status === 'completed',
                                    'bg-gray-50 text-gray-700 ring-gray-600/20' => !in_array($item->status, ['pending', 'completed'])
                                ])>
                                    {{ ucfirst($item->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <x-action-dropdown>
                                    @if($item->status === 'pending')
                                        <x-action-dropdown-item href="{{ route('admin.warehouse.put-away.edit', $item) }}">
                                            Assign Bin
                                        </x-action-dropdown-item>
                                    @endif

                                    @can('delete', $item)
                                        <form method="POST" action="{{ route('admin.warehouse.put-away.destroy', $item) }}" id="delete-putaway-form-{{ $item->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <x-action-dropdown-item onclick="document.getElementById('delete-putaway-form-{{ $item->id }}').submit()" icon="delete" variant="danger">
                                            Delete Task
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                No put away tasks pending.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 border-t border-gray-50">
            @if(method_exists($items, 'links'))
                {{ $items->links('components.pagination') }}
            @endif
        </div>
    </x-card>
</x-layouts.admin>