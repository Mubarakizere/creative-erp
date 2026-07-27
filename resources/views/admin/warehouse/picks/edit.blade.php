<x-layouts.admin title="Execute Pick">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Picking', 'url' => route('admin.warehouse.picking.index')],
                ['label' => 'Execute Task #' . substr($pick->id, 0, 8)],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Execute Pick Task #{{ substr($pick->id, 0, 8) }}</h1>
        <p class="mt-1 text-sm text-gray-500">Pick items from the specified bin for outbound shipment.</p>
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
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Target Location</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Bin Code</dt>
                        <dd class="mt-1 text-gray-900 font-bold text-xl text-blue-700">{{ $bin->code ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Zone</dt>
                        <dd class="mt-1 text-gray-900">{{ $bin->zone->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Reference</dt>
                        <dd class="mt-1 text-gray-900 font-semibold">{{ $pick->taskable->picking_number ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card>
                <form action="{{ route('admin.warehouse.picking.update', $pick) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Picking Details</h3>

                    <div class="mb-6 flex gap-4 p-4 border rounded-lg bg-gray-50">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 font-medium mb-1">Product</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $product->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $product->sku ?? '' }}</p>
                        </div>
                        <div class="flex-1 text-right">
                            <p class="text-sm text-gray-500 font-medium mb-1">Allocated Quantity</p>
                            <p class="font-bold text-gray-900 text-2xl text-blue-600">{{ number_format($allocatedQuantity, 2) }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="picked_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity Picked</label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <input type="number" step="0.01" name="picked_quantity" id="picked_quantity" 
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-4 pr-12 sm:text-lg border-gray-300 rounded-md py-3" 
                                value="{{ old('picked_quantity', $allocatedQuantity) }}" max="{{ $allocatedQuantity }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Units</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            If you cannot locate the full quantity in this bin, enter the amount you picked. The system will record a partial pick.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <x-button href="{{ route('admin.warehouse.picking.index') }}" type="secondary">Cancel</x-button>
                        <x-button submit type="primary">Confirm Pick</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
