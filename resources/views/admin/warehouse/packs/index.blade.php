<x-layouts.admin>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Packing List</h1>
            <p class="text-sm text-gray-500 mt-1">Manage outgoing shipments that require packing.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ready for Packing (Completed Pickings) -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Ready for Packing</h3>
                <p class="text-sm text-gray-500">Pickings that have been fulfilled and need to be packed.</p>
            </div>
            
            @if($pickings->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending packages</h3>
                    <p class="mt-1 text-sm text-gray-500">All completed picks have been processed.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($pickings as $pick)
                        <li class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $pick->picking_number }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">Source: {{ class_basename($pick->pickable_type) }} #{{ $pick->pickable_id }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Picked: {{ $pick->completed_at ? $pick->completed_at->diffForHumans() : 'Unknown' }}</p>
                                </div>
                                <div>
                                    <form action="{{ route('admin.warehouse.packing.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="picking_id" value="{{ $pick->id }}">
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            Start Packing
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if($pickings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $pickings->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Packings in Progress -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Packings in Progress</h3>
                <p class="text-sm text-gray-500">Packages currently being fulfilled.</p>
            </div>
            
            @if($packings->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No active packings</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any packages in progress right now.</p>
                </div>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($packings as $pack)
                        <li class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $pack->packing_number }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">From Picking: {{ $pack->picking->picking_number ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Started: {{ $pack->created_at->diffForHumans() }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('admin.warehouse.packing.edit', $pack) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Continue Packing
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if($packings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $packings->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-layouts.admin>
