<x-layouts.admin title="Initiate Cycle Count">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Cycle Counts', 'url' => route('admin.warehouse.cycle-counts.index')],
                ['label' => 'Initiate'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Initiate Cycle Count</h1>
        <p class="mt-1 text-sm text-gray-500">Trigger a new manual or scheduled count for a warehouse.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200">
            <ul class="list-disc pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-3xl">
        <x-card>
            <form action="{{ route('admin.warehouse.cycle-counts.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="cycle_count_number" class="block text-sm font-medium text-gray-700">Count Number <span class="text-red-500">*</span></label>
                        <input type="text" name="cycle_count_number" id="cycle_count_number" value="{{ old('cycle_count_number', $cycle_count_number ?? '') }}" required readonly class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 cursor-not-allowed sm:text-sm">
                        @error('cycle_count_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Target Warehouse <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select Warehouse...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Count Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="manual">Manual (Ad-hoc)</option>
                            <option value="daily">Scheduled Daily</option>
                            <option value="weekly">Scheduled Weekly</option>
                            <option value="monthly">Scheduled Monthly</option>
                            <option value="abc">ABC Analysis Count</option>
                        </select>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mt-6">
                    <p class="text-sm text-blue-800 font-medium">Note on Item Selection:</p>
                    <p class="text-xs text-blue-700 mt-1">In this system, initiating a count automatically captures a snapshot of the current inventory levels for the selected warehouse. The actual items counted will be recorded by staff on the next screen.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                    <x-button type="button" href="{{ route('admin.warehouse.cycle-counts.index') }}">Cancel</x-button>
                    <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Initiate Count Task</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>