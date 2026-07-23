<x-layouts.admin title="Inventory Valuation">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Valuation'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventory Valuation</h1>
            <p class="mt-1 text-sm text-gray-500">Real-time valuation based on configured product methods (FIFO, WAC, Standard Cost).</p>
        </div>
        <div class="text-right">
            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total System Value</div>
            <div class="text-3xl font-bold text-blue-600">${{ number_format($totalSystemValue, 2) }}</div>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Method</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Available Qty</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($valuations as $val)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $val['product']->name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $val['product']->sku ?? '—' }}</td>
                            <td class="py-3 px-4">
                                @if($val['valuation_method'] === 'FIFO')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">FIFO</span>
                                @elseif($val['valuation_method'] === 'Weighted Average')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Weighted Average</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Standard Cost</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-mono text-right">{{ number_format($val['available_quantity'], 2) }}</td>
                            <td class="py-3 px-4 text-sm font-bold text-gray-900 font-mono text-right">${{ number_format($val['total_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-gray-500">No active inventory found for valuation.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
