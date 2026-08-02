<x-layouts.admin title="Manage Cycle Count">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Cycle Counts', 'url' => route('admin.warehouse.cycle-counts.index')],
                ['label' => $cycleCount->count_number],
            ];
        @endphp
    </x-slot:breadcrumbs>


    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $cycleCount->count_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                        'variance_detected' => 'bg-red-50 text-red-700 ring-red-600/20',
                        'approved' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                    ];
                    $color = $statusColors[$cycleCount->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset mr-2">
                    {{ str_replace('_', ' ', ucfirst($cycleCount->status)) }}
                </span>
                Warehouse: {{ $cycleCount->warehouse->name }}
            </p>
        </div>
        <x-button href="{{ route('admin.warehouse.cycle-counts.index') }}">
            Back to List
        </x-button>
    </div>

    @if($cycleCount->status === 'pending')
        <!-- COUNTING INTERFACE -->
        <x-card>
            <form action="{{ route('admin.warehouse.cycle-counts.update', $cycleCount) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="record">

                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Record Physical Count</h3>
                    <p class="text-sm text-gray-500">Enter the actual quantities found in the bins.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Product</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Bin Location</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase text-right">System Qty (Expected)</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-600 uppercase text-right">Actual Counted Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($inventories as $index => $inv)
                                <tr>
                                    <td class="px-6 py-4">
                                        <input type="hidden" name="items[{{ $index }}][inventory_id]" value="{{ $inv->id }}">
                                        <p class="text-sm font-medium text-gray-900">{{ $inv->product->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $inv->product->sku ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $inv->warehouseBin->code ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 font-mono text-right">
                                        {{ number_format($inv->available_quantity, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <!-- Pre-fill with system quantity for ease of use, but highlight it so they know to change it -->
                                        <input type="number" step="0.01" name="items[{{ $index }}][counted_quantity]" value="{{ $inv->available_quantity }}" required class="w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right font-mono font-bold text-blue-700 bg-blue-50">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No inventory found in this warehouse to count.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($inventories->isNotEmpty())
                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Submit Count Results</x-button>
                    </div>
                @endif
            </form>
        </x-card>
    
    @else
        <!-- REVIEW / APPROVAL INTERFACE -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <x-card>
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Count Results & Variances</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase">Product & Bin</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase text-right">Expected</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase text-right">Counted</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-600 uppercase text-right">Variance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($cycleCount->stockCount->items as $item)
                                    @php
                                        $inv = $item->inventory;
                                        $variance = $item->variance;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900">{{ $inv->product->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">Bin: {{ $inv->warehouseBin->code ?? 'Unassigned' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 font-mono text-right">
                                            {{ number_format($item->expected_quantity, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-mono text-right font-medium">
                                            {{ number_format($item->counted_quantity, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-right font-bold">
                                            @if($variance > 0)
                                                <span class="text-green-600">+{{ number_format($variance, 2) }}</span>
                                            @elseif($variance < 0)
                                                <span class="text-red-600">{{ number_format($variance, 2) }}</span>
                                            @else
                                                <span class="text-gray-400">0.00</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <!-- Approval Action -->
                @if($cycleCount->status === 'variance_detected')
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-red-700 mb-2">Action Required</h3>
                            <p class="text-sm text-gray-700 mb-6">Discrepancies were found during the physical count. Approving this will immediately adjust system inventory to match the counted values and post an Accounting adjustment entry.</p>

                            <form action="{{ route('admin.warehouse.cycle-counts.update', $cycleCount) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" onclick="return confirm('Are you sure you want to approve these variances and adjust inventory?')" class="w-full py-2.5 bg-red-600 text-white font-medium rounded-lg shadow-sm hover:bg-red-700 transition-colors">
                                    Approve & Adjust Inventory
                                </button>
                            </form>
                        </div>
                    </x-card>
                @elseif($cycleCount->status === 'approved' || $cycleCount->status === 'completed')
                    <x-card>
                        <div class="p-6 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Count Finalized</h3>
                            <p class="text-sm text-gray-500 mt-1">This cycle count has been completed.</p>
                            @if($cycleCount->approvedBy)
                                <p class="text-xs text-gray-400 mt-4">Approved by {{ $cycleCount->approvedBy->name }}</p>
                            @endif
                        </div>
                    </x-card>
                @endif
            </div>
        </div>
    @endif
</x-layouts.admin>