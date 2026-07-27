<x-layouts.admin title="Execute Put Away">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Put Aways', 'url' => route('admin.warehouse.put-away.index')],
                ['label' => 'Execute Task #' . $item->id],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Execute Put Away Task #{{ $item->id }}</h1>
        <p class="mt-1 text-sm text-gray-500">Review details and assign incoming goods to a bin.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <x-card>
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Item Details</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Product</dt>
                        <dd class="mt-1 text-gray-900 font-semibold">{{ $item->taskable->product->name ?? 'N/A' }}</dd>
                        <dd class="text-gray-500 text-xs">{{ $item->taskable->product->sku ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Incoming Quantity</dt>
                        <dd class="mt-1 text-gray-900 font-semibold text-lg">{{ number_format($item->taskable->quantity_received ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $item->notes ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card>
                <form action="{{ route('admin.warehouse.put-away.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Bin Assignment</h3>

                    @if($suggestedBin)
                        <div class="mb-6 bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 w-full">
                                    <h4 class="text-sm font-medium text-green-800">Suggested Bin: {{ $suggestedBin->code }}</h4>
                                    <p class="mt-1 text-sm text-green-700">
                                        This bin currently holds {{ number_format($suggestedBin->current_quantity, 2) }} units. Capacity: {{ $suggestedBin->capacity ? number_format($suggestedBin->capacity, 2) : 'Unlimited' }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label for="warehouse_bin_id" class="block text-sm font-medium text-gray-700 mb-1">Select Destination Bin</label>
                        <select id="warehouse_bin_id" name="warehouse_bin_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Select a Bin --</option>
                            @foreach($bins as $bin)
                                <option value="{{ $bin->id }}" @selected(old('warehouse_bin_id', $suggestedBin?->id) == $bin->id)>
                                    {{ $bin->code }} 
                                    (Zone: {{ $bin->zone->name }}, 
                                     Current Qty: {{ number_format($bin->current_quantity, 2) }},
                                     Capacity: {{ $bin->capacity ? number_format($bin->capacity, 2) : 'Unlimited' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">If you override the suggested bin, please ensure the selected bin has sufficient capacity.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <x-button href="{{ route('admin.warehouse.put-away.index') }}" type="secondary">Cancel</x-button>
                        <x-button submit type="primary">Confirm Put Away</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.admin>