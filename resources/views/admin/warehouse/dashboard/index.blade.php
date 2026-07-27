<x-layouts.admin title="Warehouse Dashboard">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Warehouse Ops', 'url' => '#'],
                ['label' => 'Dashboard'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Warehouse Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Real-time overview of warehouse capacity and pending operational flows.</p>
        </div>
        <div class="flex gap-2">
            <x-button href="{{ route('admin.warehouse.tasks.index') }}" variant="secondary">View All Tasks</x-button>
        </div>
    </div>

    <!-- Utilization & Capacity Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Utilization Widget -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative group">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Warehouse Utilization</h2>
                        <p class="text-sm text-gray-500">Current fill level across all bins</p>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                
                <div class="flex items-end gap-3 mb-4">
                    <span class="text-4xl font-bold text-gray-900">{{ $metrics['utilization'] }}%</span>
                    <span class="text-sm font-medium {{ $metrics['utilization'] > 85 ? 'text-red-500' : 'text-green-500' }} mb-1">
                        {{ $metrics['utilization'] > 85 ? 'High Utilization' : 'Optimal Capacity' }}
                    </span>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-3 mb-1">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $metrics['utilization'] }}%"></div>
                </div>
            </div>
            <!-- Decorative accent -->
            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600 w-full transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
        </div>

        <!-- Bin Capacity Widget -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative group">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Bin Capacity Overview</h2>
                        <p class="text-sm text-gray-500">Distribution of storage bins</p>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-6">
                    <div class="text-center p-3 rounded-lg bg-green-50">
                        <div class="text-2xl font-bold text-green-700">{{ $metrics['bin_capacity']['empty'] }}</div>
                        <div class="text-xs font-medium text-green-600 uppercase tracking-wider mt-1">Empty</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-yellow-50">
                        <div class="text-2xl font-bold text-yellow-700">{{ $metrics['bin_capacity']['partial'] }}</div>
                        <div class="text-xs font-medium text-yellow-600 uppercase tracking-wider mt-1">Partial</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-red-50">
                        <div class="text-2xl font-bold text-red-700">{{ $metrics['bin_capacity']['full'] }}</div>
                        <div class="text-xs font-medium text-red-600 uppercase tracking-wider mt-1">Full</div>
                    </div>
                </div>
            </div>
            <!-- Decorative accent -->
            <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 w-full transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
        </div>
    </div>

    <!-- Pending Operations Grid -->
    <h2 class="text-lg font-bold text-gray-900 mb-4">Operational Flows</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Pending Picks -->
        <a href="{{ route('admin.warehouse.picking.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-amber-50 rounded-xl group-hover:bg-amber-100 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['pending_picks'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">Pending Picks</h3>
            <p class="text-sm text-gray-500 mt-1">Orders waiting to be picked from bins</p>
        </a>

        <!-- Pending Packing -->
        <a href="{{ route('admin.warehouse.packing.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['pending_packing'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">Pending Packing</h3>
            <p class="text-sm text-gray-500 mt-1">Picked items waiting to be packed</p>
        </a>

        <!-- Pending Shipments -->
        <a href="{{ route('admin.warehouse.shipments.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-purple-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-purple-50 rounded-xl group-hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['pending_shipments'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">Pending Shipments</h3>
            <p class="text-sm text-gray-500 mt-1">Packed boxes waiting for dispatch</p>
        </a>
        
        <!-- Pending Returns -->
        <a href="{{ route('admin.warehouse.returns.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-rose-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-rose-50 rounded-xl group-hover:bg-rose-100 transition-colors">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['pending_returns'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">Pending Returns</h3>
            <p class="text-sm text-gray-500 mt-1">Returns waiting for inspection</p>
        </a>

        <!-- Warehouse Tasks -->
        <a href="{{ route('admin.warehouse.tasks.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-cyan-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-cyan-50 rounded-xl group-hover:bg-cyan-100 transition-colors">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['warehouse_tasks'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">General Tasks</h3>
            <p class="text-sm text-gray-500 mt-1">Pending general warehouse tasks</p>
        </a>

        <!-- Cycle Counts -->
        <a href="{{ route('admin.warehouse.cycle-counts.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-teal-300 hover:shadow-md transition-all group">
            <div class="flex justify-between items-center mb-4">
                <div class="p-3 bg-teal-50 rounded-xl group-hover:bg-teal-100 transition-colors">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $metrics['cycle_counts'] }}</span>
            </div>
            <h3 class="text-md font-semibold text-gray-800">Cycle Counts</h3>
            <p class="text-sm text-gray-500 mt-1">Counts requiring attention or approval</p>
        </a>
    </div>

</x-layouts.admin>
