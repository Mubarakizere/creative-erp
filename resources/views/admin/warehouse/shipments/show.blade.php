<x-layouts.admin title="View Shipment">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Shipments', 'url' => route('admin.warehouse.shipments.index')],
                ['label' => $shipment->shipment_number],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 p-4 border border-green-200">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $shipment->shipment_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                        'prepared' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'shipped' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                        'delivered' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                    ];
                    $color = $statusColors[$shipment->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset">
                    {{ ucfirst($shipment->status) }}
                </span>
            </p>
        </div>
        <x-button href="{{ route('admin.warehouse.shipments.index') }}">
            Back to List
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2">
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Shipment Details</h3>
                    <dl class="divide-y divide-gray-100">
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Shipment Number</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0 font-mono">{{ $shipment->shipment_number }}</dd>
                        </div>
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Carrier</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $shipment->carrier ?? 'Not assigned' }}</dd>
                        </div>
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Tracking Number</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0 font-mono">{{ $shipment->tracking_number ?? '—' }}</dd>
                        </div>
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $shipment->shipping_notes ?? 'None' }}</dd>
                        </div>
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Shipped At</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $shipment->shipped_at ? $shipment->shipped_at->format('M d, Y H:i') : '—' }}</dd>
                        </div>
                        <div class="px-0 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-900">Delivered At</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $shipment->delivered_at ? $shipment->delivered_at->format('M d, Y H:i') : '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </x-card>

            <!-- Attached Packages -->
            <x-card class="mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attached Packages</h3>
                    @if($packings->isEmpty())
                        <p class="text-sm text-gray-400 italic">No packages attached to this shipment.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Package #</th>
                                        <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Weight</th>
                                        <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Dimensions (L×W×H)</th>
                                        <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($packings as $pack)
                                        <tr>
                                            <td class="py-2 px-3 text-sm font-medium text-gray-900">{{ $pack->packing_number }}</td>
                                            <td class="py-2 px-3 text-sm text-gray-700">{{ $pack->total_weight ?? 0 }} kg</td>
                                            <td class="py-2 px-3 text-sm text-gray-700 font-mono">
                                                @if($pack->length || $pack->width || $pack->height)
                                                    {{ $pack->length ?? '—' }} × {{ $pack->width ?? '—' }} × {{ $pack->height ?? '—' }} cm
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-sm text-gray-700">{{ ucfirst($pack->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        <!-- Workflow Actions -->
        <div>
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Actions</h3>

                    <!-- Status Timeline -->
                    <div class="mb-6 space-y-3">
                        @php
                            $stages = ['pending', 'prepared', 'shipped', 'delivered'];
                            $currentIdx = array_search($shipment->status, $stages);
                            if ($currentIdx === false) $currentIdx = -1;
                        @endphp
                        @foreach($stages as $idx => $stage)
                            <div class="flex items-center gap-3">
                                @if($idx < $currentIdx || $shipment->status === $stage)
                                    <div class="w-6 h-6 rounded-full {{ $shipment->status === $stage ? 'bg-blue-600' : 'bg-green-500' }} flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 bg-white"></div>
                                @endif
                                <span class="text-sm {{ $shipment->status === $stage ? 'font-bold text-gray-900' : ($idx < $currentIdx ? 'text-green-700' : 'text-gray-400') }}">{{ ucfirst($stage) }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if($shipment->status === 'cancelled')
                        <div class="rounded-lg bg-red-50 p-4 border border-red-200">
                            <p class="text-sm text-red-700">This shipment has been cancelled.</p>
                        </div>
                    @else
                        <form action="{{ route('admin.warehouse.shipments.update', $shipment) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            @if($shipment->status === 'pending')
                                <div class="space-y-3">
                                    <div>
                                        <label for="carrier" class="block text-xs font-medium text-gray-600">Carrier</label>
                                        <input type="text" name="carrier" id="carrier" value="{{ $shipment->carrier }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="FedEx, DHL...">
                                    </div>
                                    <div>
                                        <label for="tracking_number" class="block text-xs font-medium text-gray-600">Tracking #</label>
                                        <input type="text" name="tracking_number" id="tracking_number" value="{{ $shipment->tracking_number }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono" placeholder="1Z999AA10123456784">
                                    </div>
                                    <div>
                                        <label for="shipping_notes" class="block text-xs font-medium text-gray-600">Notes</label>
                                        <textarea name="shipping_notes" id="shipping_notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $shipment->shipping_notes }}</textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="action" value="prepare">
                                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                    Mark as Prepared
                                </button>
                            @elseif($shipment->status === 'prepared')
                                <input type="hidden" name="action" value="dispatch">
                                <input type="hidden" name="carrier" value="{{ $shipment->carrier }}">
                                <input type="hidden" name="tracking_number" value="{{ $shipment->tracking_number }}">
                                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                    Dispatch Shipment
                                </button>
                            @elseif($shipment->status === 'shipped')
                                <input type="hidden" name="action" value="deliver">
                                <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                    Mark as Delivered
                                </button>
                            @endif
                        </form>

                        @if(in_array($shipment->status, ['pending', 'prepared']))
                            <form action="{{ route('admin.warehouse.shipments.update', $shipment) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this shipment?')" class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors border border-red-200">
                                    Cancel Shipment
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>