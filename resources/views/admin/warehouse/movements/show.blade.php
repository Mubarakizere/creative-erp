<x-layouts.admin title="View Movement">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Movements', 'url' => route('admin.warehouse.movements.index')],
                ['label' => $movement->movement_number],
            ];
        @endphp
    </x-slot:breadcrumbs>


    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $movement->movement_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                    ];
                    $color = $statusColors[$movement->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $color }} ring-1 ring-inset">
                    {{ ucfirst($movement->status) }}
                </span>
            </p>
        </div>
        <x-button href="{{ route('admin.warehouse.movements.index') }}">
            Back to List
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Movement Details -->
            <x-card>
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-medium text-gray-900">Transfer Details</h3>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">{{ $movement->product->name ?? 'Unknown Product' }}</p>
                        <p class="text-xs text-gray-500">Qty: {{ number_format($movement->quantity, 2) }}</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                    <!-- Source -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">From Location</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500">Warehouse</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $movement->sourceWarehouse->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Zone</dt>
                                <dd class="text-sm text-gray-700">{{ $movement->sourceZone->name ?? 'None' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Bin</dt>
                                <dd class="text-sm text-gray-700">{{ $movement->sourceBin->code ?? 'None' }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Arrow separator on desktop -->
                    <div class="hidden md:flex absolute inset-y-0 left-1/2 -ml-3 items-center justify-center">
                        <div class="bg-white p-1 rounded-full border border-gray-200 shadow-sm text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Destination -->
                    <div>
                        <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-3">To Location</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500">Warehouse</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $movement->destinationWarehouse->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Zone</dt>
                                <dd class="text-sm text-gray-700">{{ $movement->destinationZone->name ?? 'None' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Bin</dt>
                                <dd class="text-sm text-gray-700">{{ $movement->destinationBin->code ?? 'None' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </x-card>

            <!-- Execution History -->
            @if($movement->status === 'completed' && $history->isNotEmpty())
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Type</th>
                                    <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase text-right">Quantity Change</th>
                                    <th class="py-2 px-3 bg-gray-50 text-xs font-semibold text-gray-600 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($history as $tx)
                                    <tr>
                                        <td class="py-3 px-3 text-sm">
                                            @if($tx->type === 'transfer_out')
                                                <span class="inline-flex items-center text-red-600 font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m12 0l-4-4m4 4l-4 4"/></svg>
                                                    Transfer Out
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-green-600 font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h12m-12 0l4-4m-4 4l4 4"/></svg>
                                                    Transfer In
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-sm font-mono text-right {{ $tx->quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $tx->quantity > 0 ? '+' : '' }}{{ number_format($tx->quantity, 2) }}
                                        </td>
                                        <td class="py-3 px-3 text-sm text-gray-500">{{ $tx->date->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
            @endif
        </div>

        <div>
            <!-- Approval Workflow -->
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Actions</h3>
                    
                    @if($movement->reason)
                        <div class="mb-6 p-3 bg-yellow-50 text-yellow-800 text-sm rounded-lg border border-yellow-100">
                            <strong>Reason:</strong> {{ $movement->reason }}
                        </div>
                    @endif

                    @if($movement->status === 'pending')
                        <div class="space-y-3">
                            <form action="{{ route('admin.warehouse.movements.update', $movement) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="execute">
                                <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                    Approve & Execute Movement
                                </button>
                            </form>

                            <form action="{{ route('admin.warehouse.movements.update', $movement) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this request?')" class="w-full px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors border border-red-200">
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    @elseif($movement->status === 'completed')
                        <div class="rounded-lg bg-green-50 p-4 border border-green-200">
                            <p class="text-sm text-green-700 font-medium">Movement Executed</p>
                            <p class="text-xs text-green-600 mt-1">Approved by: {{ $movement->approvedBy->name ?? 'System' }}</p>
                            <p class="text-xs text-green-600">On: {{ $movement->approved_at->format('M d, Y H:i') }}</p>
                        </div>
                    @else
                        <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
                            <p class="text-sm text-gray-700">This request is {{ $movement->status }}.</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.admin>