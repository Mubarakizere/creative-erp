<x-layouts.admin title="Stock Reservations">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
                ['label' => 'Reservations'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Reservations</h1>
            <p class="mt-1 text-sm text-gray-500">Manage reserved stock for quotations, invoices, and projects.</p>
        </div>
        @can('create', App\Models\InventoryReservation::class)
            <x-button type="primary" href="{{ route('admin.inventory.reservations.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Reservation
            </x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservations as $reservation)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $reservation->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                                {{ $reservation->product->name }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $reservation->warehouse->name }}
                                @if($reservation->zone)
                                    <span class="text-xs text-gray-400 block">{{ $reservation->zone->name }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ number_format($reservation->quantity, 2) }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ class_basename($reservation->reference_type) }} 
                                @if($reservation->reference)
                                    #{{ substr($reservation->reference_id, 0, 8) }}
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($reservation->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Active</span>
                                @elseif($reservation->status === 'released')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Released</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($reservation->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($reservation->status === 'active')
                                    <form action="{{ route('admin.inventory.reservations.release', $reservation) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to release this stock back to available?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-900">Release</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-500">No active reservations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $reservations->links('components.pagination') }}
        </div>
    </x-card>
</x-layouts.admin>
