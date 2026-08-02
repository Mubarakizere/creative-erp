<x-layouts.admin title="Warehouse Picking">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Picking', 'url' => route('admin.warehouse.picking.index')],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Warehouse Picking</h1>
            <p class="mt-1 text-sm text-gray-500">Manage and execute picking tasks for outbound orders.</p>
        </div>
    </div>


    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Task ID</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pick List Ref</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bin</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">To Pick</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        @php
                            $data = json_decode($item->notes, true);
                            $product = \App\Models\Product::find($data['product_id']);
                            $bin = \App\Models\WarehouseBin::find($data['bin_id']);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                #{{ substr($item->id, 0, 8) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $item->taskable->picking_number ?? 'Unknown' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $product->sku ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700 font-medium">
                                {{ $bin->code ?? 'Unknown' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700 text-right font-bold">
                                {{ number_format($data['quantity'] ?? 0, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <x-button href="{{ route('admin.warehouse.picking.edit', $item) }}" type="primary" size="sm">
                                    Execute Pick
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm">No picking tasks pending.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </x-card>
</x-layouts.admin>
