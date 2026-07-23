<x-layouts.admin title="Stock Count Sheet">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Stock Counts', 'url' => route('admin.inventory.stock-counts.index')],
                ['label' => 'Count Sheet'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ ucfirst($stockCount->type) }} Count — {{ $stockCount->warehouse->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Started {{ $stockCount->created_at->format('M d, Y H:i') }}
                @if($stockCount->createdByUser) by {{ $stockCount->createdByUser->name }} @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($stockCount->status === 'approved')
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approved
                    @if($stockCount->approvedBy) by {{ $stockCount->approvedBy->name }} @endif
                </span>
            @elseif($stockCount->status === 'counted')
                <form action="{{ route('admin.inventory.stock-counts.approve', $stockCount) }}" method="POST" onsubmit="return confirm('Approve this count and generate adjustment transactions for all variances?');">
                    @csrf
                    <x-button type="primary" submit>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve Count
                    </x-button>
                </form>
            @endif
        </div>
    </div>

    @if($stockCount->variance_detected)
        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-xl">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span class="text-sm font-medium text-orange-800">Variances detected in this count.</span>
            </div>
        </div>
    @endif

    <x-card>
        <form action="{{ route('admin.inventory.stock-counts.update', $stockCount) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                            <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">System Qty</th>
                            <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Counted Qty</th>
                            <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Variance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($stockCount->items as $index => $item)
                            <tr class="{{ $item->variance != 0 ? 'bg-orange-50/50' : '' }}">
                                <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                                    {{ $item->product->name }}
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600 text-right font-mono">{{ number_format($item->system_quantity, 2) }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($stockCount->status === 'approved')
                                        <span class="text-sm text-gray-900 font-mono">{{ number_format($item->counted_quantity, 2) }}</span>
                                    @else
                                        <input type="number" step="0.01" min="0"
                                               name="items[{{ $index }}][counted_quantity]"
                                               value="{{ old('items.' . $index . '.counted_quantity', $item->counted_quantity) }}"
                                               class="w-28 text-right border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                                               required>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($item->counted_quantity !== null)
                                        <span class="text-sm font-mono font-medium {{ $item->variance > 0 ? 'text-green-600' : ($item->variance < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                            {{ $item->variance > 0 ? '+' : '' }}{{ number_format($item->variance, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($stockCount->status !== 'approved')
                <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.inventory.stock-counts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                    <x-button type="primary" submit>Save Counted Quantities</x-button>
                </div>
            @endif
        </form>
    </x-card>
</x-layouts.admin>
