<x-layouts.admin title="Edit Shipment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Shipments', 'url' => route('admin.warehouse.shipments.index')],
                ['label' => $shipment->shipment_number],
                ['label' => 'Edit'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Shipment: {{ $shipment->shipment_number }}</h1>
    </div>

    <x-card>
        <form action="{{ route('admin.warehouse.shipments.update', $shipment) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="carrier" class="block text-sm font-medium text-gray-700">Carrier</label>
                    <input type="text" name="carrier" id="carrier" value="{{ $shipment->carrier }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="FedEx, DHL...">
                </div>
                <div>
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700">Tracking Number</label>
                    <input type="text" name="tracking_number" id="tracking_number" value="{{ $shipment->tracking_number }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono">
                </div>
            </div>

            <div>
                <label for="shipping_notes" class="block text-sm font-medium text-gray-700">Shipping Notes</label>
                <textarea name="shipping_notes" id="shipping_notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ $shipment->shipping_notes }}</textarea>
            </div>

            <input type="hidden" name="action" value="prepare">

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-200">
                <x-button type="button" href="{{ route('admin.warehouse.shipments.show', $shipment) }}">Cancel</x-button>
                <x-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Save & Mark as Prepared</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>