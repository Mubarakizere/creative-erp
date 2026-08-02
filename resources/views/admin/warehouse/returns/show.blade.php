<x-layouts.admin title="Inspect Return">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Returns', 'url' => route('admin.warehouse.returns.index')],
                ['label' => $return->return_number],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $return->return_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                        'restocked' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'disposed' => 'bg-red-50 text-red-700 ring-red-600/20',
                    ];
                    $color = $statusColors[$return->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                    $typeLabels = [
                        'customer_return' => 'Customer Return',
                        'supplier_return' => 'Supplier Return',
                        'damaged_stock' => 'Damaged Stock',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset mr-2">
                    {{ ucfirst($return->status) }}
                </span>
                {{ $typeLabels[$return->type] ?? ucfirst($return->type) }}
            </p>
        </div>
        <x-button href="{{ route('admin.warehouse.returns.index') }}">
            Back to List
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Return Details -->
            <x-card>
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-medium text-gray-900">Returned Items</h3>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">Receiving: {{ $return->warehouse->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">Total Items: {{ collect($return->items)->sum('quantity') }}</p>
                    </div>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Qty</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-right">Unit Val</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($return->items as $item)
                                @php
                                    $product = $products[$item['product_id']] ?? null;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product ? $product->name : 'Unknown Product' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-mono text-right">{{ number_format($item['quantity'], 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ number_format($item['unit_cost'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Inspection Workflow (Only if Pending) -->
            @if($return->status === 'pending')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Inspection & Processing</h3>
                    </div>
                    
                    <div class="p-6" x-data="{ mode: 'restocked' }">
                        <!-- Mode Toggles -->
                        <div class="flex gap-4 mb-6">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="mode_toggle" value="restocked" x-model="mode" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition-all text-center">
                                    <div class="font-bold text-green-700 mb-1">Restock to Inventory</div>
                                    <p class="text-xs text-green-600/80">Items are in good condition.</p>
                                </div>
                            </label>
                            
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="mode_toggle" value="disposed" x-model="mode" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition-all text-center">
                                    <div class="font-bold text-red-700 mb-1">Dispose & Write Off</div>
                                    <p class="text-xs text-red-600/80">Items are damaged beyond repair.</p>
                                </div>
                            </label>
                        </div>

                        <!-- Restock Form -->
                        <form x-show="mode === 'restocked'" action="{{ route('admin.warehouse.returns.update', $return) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="restocked">
                            
                            <div class="space-y-4 mb-6">
                                <h4 class="text-sm font-bold text-gray-900 border-b pb-2">Assign Destination Bins</h4>
                                @foreach($return->items as $index => $item)
                                    @php
                                        $product = $products[$item['product_id']] ?? null;
                                    @endphp
                                    <div class="flex gap-4 items-center bg-gray-50 p-3 rounded-lg">
                                        <input type="hidden" name="restock_items[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                                        <input type="hidden" name="restock_items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                                        <input type="hidden" name="restock_items[{{ $index }}][unit_cost]" value="{{ $item['unit_cost'] ?? 0 }}">
                                        
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $product ? $product->name : 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">Qty: {{ number_format($item['quantity'], 2) }}</p>
                                        </div>
                                        <div class="w-1/2">
                                            <select name="restock_items[{{ $index }}][bin_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                                <option value="">Select Bin...</option>
                                                @foreach($bins as $bin)
                                                    <option value="{{ $bin->id }}">{{ $bin->code }} (Cap: {{ $bin->capacity }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700">Inspection Notes</label>
                                <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Are you sure you want to restock these items?')" class="w-full py-3 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors">
                                Process Restock
                            </button>
                        </form>

                        <!-- Dispose Form -->
                        <form x-show="mode === 'disposed'" action="{{ route('admin.warehouse.returns.update', $return) }}" method="POST" style="display: none;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="disposed">

                            <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-100">
                                <p class="text-sm text-red-800">Warning: Processing this as Disposed will completely remove these items from tracked inventory.</p>
                                @if($return->requires_accounting_adjustment)
                                    <p class="text-sm font-bold text-red-800 mt-2">Accounting Write-off will be triggered.</p>
                                @endif
                            </div>
                            
                            @if($return->requires_accounting_adjustment)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700">Total Loss Amount <span class="text-red-500">*</span></label>
                                    @php
                                        // Calculate total suggested value
                                        $suggestedValue = collect($return->items)->sum(function($item) {
                                            return $item['quantity'] * ($item['unit_cost'] ?? 0);
                                        });
                                    @endphp
                                    <input type="number" step="0.01" name="loss_amount" value="{{ $suggestedValue }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                </div>
                            @endif

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700">Disposal Notes (Required)</label>
                                <textarea name="notes" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                            </div>

                            <button type="submit" onclick="return confirm('Are you sure you want to DISPOSE of these items permanently?')" class="w-full py-3 bg-red-600 text-white font-medium rounded-lg shadow-sm hover:bg-red-700 transition-colors">
                                Dispose & Write Off
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <!-- Status Sidebar -->
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inspection Status</h3>
                    
                    @if($return->status === 'pending')
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm font-medium text-yellow-800">Awaiting Inspection</p>
                            <p class="text-xs text-yellow-700 mt-1">Please use the workflow on the left to process this return.</p>
                        </div>
                    @else
                        <div class="p-4 rounded-lg {{ $return->status === 'restocked' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border">
                            <p class="text-sm font-medium {{ $return->status === 'restocked' ? 'text-green-800' : 'text-red-800' }}">
                                Processed as: {{ ucfirst($return->status) }}
                            </p>
                            <p class="text-xs text-gray-600 mt-2">By: {{ $return->inspectedBy->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-600">On: {{ $return->inspected_at->format('M d, Y H:i') }}</p>
                        </div>
                        
                        @if($return->inspection_notes)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Notes</h4>
                                <p class="text-sm text-gray-700 italic">{{ $return->inspection_notes }}</p>
                            </div>
                        @endif
                        
                        @if($return->requires_accounting_adjustment && $return->status === 'disposed')
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-bold text-gray-700">Accounting Notified</p>
                                <p class="text-xs text-gray-500 mt-1">Write-off requested.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- Simple Alpine fallback for JS toggle -->
    <script>
        if (typeof Alpine === 'undefined') {
            document.addEventListener('DOMContentLoaded', () => {
                const radios = document.querySelectorAll('input[name="mode_toggle"]');
                const forms = {
                    'restocked': document.querySelector('form[action*="returns"][x-show="mode === \'restocked\'"]'),
                    'disposed': document.querySelector('form[action*="returns"][x-show="mode === \'disposed\'"]')
                };

                radios.forEach(radio => {
                    radio.addEventListener('change', (e) => {
                        forms.restocked.style.display = e.target.value === 'restocked' ? 'block' : 'none';
                        forms.disposed.style.display = e.target.value === 'disposed' ? 'block' : 'none';
                    });
                });
            });
        }
    </script>
</x-layouts.admin>