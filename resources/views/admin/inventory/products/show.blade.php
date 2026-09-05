<x-layouts.admin :title="$product->name . ' - Material Details'">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Materials', 'url' => route('admin.inventory.products.index')],
                ['label' => $product->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Top Header Banner --}}
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
        <div class="flex items-start gap-4">
            @if($product->image)
                <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-2xl object-cover border border-gray-200 shadow-xs">
            @else
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-bold text-xl shadow-md shrink-0">
                    {{ strtoupper(substr($product->name, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <span class="text-xs px-2.5 py-1 rounded-lg font-medium bg-blue-50 text-blue-700 border border-blue-100 capitalize">
                        {{ str_replace('_', ' ', $product->type) }}
                    </span>
                    @php
                        $statusType = match($product->status) {
                            'active' => 'success',
                            'inactive' => 'warning',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ ucfirst($product->status) }}</x-badge>
                </div>
                <p class="mt-1 text-sm text-gray-500 flex items-center gap-3">
                    <span><strong>SKU:</strong> {{ $product->sku }}</span>
                    @if($product->barcode)
                        <span>• <strong>Barcode:</strong> {{ $product->barcode }}</span>
                    @endif
                    @if($product->unit)
                        <span>• <strong>UoM:</strong> {{ $product->unit->name }} ({{ $product->unit->code ?? $product->unit->abbreviation }})</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            @can('update', $product)
                <x-button type="secondary" href="{{ route('admin.inventory.products.edit', $product) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Details
                </x-button>
            @endcan

            @if(Route::has('admin.projects.material-issues.create'))
                <x-button type="primary" href="{{ route('admin.projects.material-issues.create', ['product_id' => $product->id]) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Issue to Project
                </x-button>
            @endif

            @can('delete', $product)
                <div x-data="{ showDeleteModal: false }">
                    <button type="button" @click="showDeleteModal = true" class="p-2.5 text-gray-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 border border-gray-200 transition-colors cursor-pointer" title="Delete Material">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>

                    <template x-teleport="body">
                        <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @keydown.escape.window="showDeleteModal = false">
                            <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="showDeleteModal = false"></div>
                            <div class="flex min-h-full items-center justify-center p-4">
                                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden" @click.stop>
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                        <div class="text-lg font-bold text-gray-900">Delete Material</div>
                                        <button @click="showDeleteModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="px-6 py-4 text-center">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4 border border-rose-200">
                                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete "{{ $product->name }}"?</h3>
                                        <p class="text-xs text-gray-500">Are you sure you want to delete this material? It will be moved to archived materials.</p>
                                    </div>
                                    <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 justify-end">
                                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-xs">Cancel</button>
                                        <form action="{{ route('admin.inventory.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
                                                Delete Material
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endcan
        </div>
    </div>

    {{-- KPI Metric Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card class="bg-gradient-to-br from-blue-50/50 to-white border-blue-100">
            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total On-Hand Stock</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-2">
                {{ number_format($totalStock, 2) }} <span class="text-sm font-normal text-gray-500">{{ $product->unit->code ?? 'units' }}</span>
            </h3>
            <p class="text-xs text-gray-500 mt-1">Across all site warehouses</p>
        </x-card>

        <x-card class="bg-gradient-to-br from-emerald-50/50 to-white border-emerald-100">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Available Stock</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-2">
                {{ number_format($availableStock, 2) }} <span class="text-sm font-normal text-gray-500">{{ $product->unit->code ?? 'units' }}</span>
            </h3>
            <p class="text-xs text-gray-500 mt-1">Reserved: {{ number_format($totalReserved, 2) }}</p>
        </x-card>

        <x-card class="bg-gradient-to-br from-amber-50/50 to-white border-amber-100">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Last Purchase Cost</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-2">
                {{ $product->cost_price ? number_format($product->cost_price, 0) . ' RWF' : 'Not Set' }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Method: {{ $product->valuation_method ?? 'FIFO' }}</p>
        </x-card>

        <x-card class="bg-gradient-to-br from-purple-50/50 to-white border-purple-100">
            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Primary Supplier</p>
            <h3 class="text-lg font-bold text-gray-900 mt-2 truncate">
                {{ $product->defaultSupplier?->name ?? 'None Assigned' }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Part #: {{ $product->supplier_sku ?? 'N/A' }}</p>
        </x-card>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Stock per Warehouse & Procurement History --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Warehouse Stock Breakdown --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Warehouse Stock Breakdown</h3>
                    <span class="text-xs font-medium text-gray-500">{{ $product->inventory->count() }} Location(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Warehouse</th>
                                <th class="px-4 py-3 text-right">Physical Stock</th>
                                <th class="px-4 py-3 text-right">Reserved</th>
                                <th class="px-4 py-3 text-right">Available</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($product->inventory as $inv)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $inv->warehouse->name ?? 'Main Storage' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">
                                        {{ number_format($inv->available_quantity, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-amber-600">
                                        {{ number_format($inv->reserved_quantity, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-600">
                                        {{ number_format(max(0, $inv->available_quantity - $inv->reserved_quantity), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        No warehouse inventory records recorded for this material.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Recent Material Issue Slips --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Recent Project Material Issues</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Issue Date</th>
                                <th class="px-4 py-3">Project</th>
                                <th class="px-4 py-3 text-right">Quantity</th>
                                <th class="px-4 py-3 text-right">Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($product->materialIssueItems->take(5) as $issueItem)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $issueItem->projectMaterialIssue?->issue_date ? \Carbon\Carbon::parse($issueItem->projectMaterialIssue->issue_date)->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $issueItem->projectMaterialIssue?->project?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                        {{ number_format($issueItem->quantity, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700">
                                        {{ number_format($issueItem->unit_cost ?? $product->cost_price, 0) }} RWF
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        No recent project material issues logged.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Right Column: Material Settings & Specifications --}}
        <div class="space-y-6">
            {{-- Material Specifications Card --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Specifications & Details</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Category:</span>
                        <span class="font-semibold text-gray-900">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Brand / Manufacturer:</span>
                        <span class="font-semibold text-gray-900">{{ $product->brand?->name ?? 'Generic / Standard' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Tax Rate:</span>
                        <span class="font-semibold text-gray-900">{{ $product->tax?->name ?? 'Tax Exempt' }}</span>
                    </div>
                    @if($product->weight)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Unit Weight:</span>
                            <span class="font-semibold text-gray-900">{{ number_format($product->weight, 2) }} kg</span>
                        </div>
                    @endif
                    @if($product->dimensions)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Dimensions:</span>
                            <span class="font-semibold text-gray-900">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                </div>

                @if($product->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Description / Notes</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif
            </x-card>

            {{-- Reorder & Control Rules Card --}}
            <x-card>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Control & Reorder Rules</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Minimum Stock Level:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($product->minimum_stock, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Reorder Trigger Level:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($product->reorder_level, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Safety Buffer Stock:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($product->safety_stock, 2) }}</span>
                    </div>
                    <div class="pt-2 grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $product->track_inventory ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                            <span class="text-gray-600">Track Stock</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $product->allow_negative_stock ? 'bg-amber-500' : 'bg-gray-300' }}"></span>
                            <span class="text-gray-600">Negative Stock</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $product->serial_numbers ? 'bg-blue-500' : 'bg-gray-300' }}"></span>
                            <span class="text-gray-600">Track Serials</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $product->batch_numbers ? 'bg-indigo-500' : 'bg-gray-300' }}"></span>
                            <span class="text-gray-600">Track Batches</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
