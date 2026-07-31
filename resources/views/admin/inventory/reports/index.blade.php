<x-layouts.admin title="Inventory Reports">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Inventory Report Center</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Generate, view, and export detailed inventory analytics.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', \App\Models\ReportTemplate::class)
                <a href="{{ route('admin.reports.builder') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors whitespace-nowrap min-h-[42px]">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Custom Report
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($reports as $slug => $name)
            <x-card class="flex flex-col justify-between group hover:shadow-md transition-all duration-200 border-gray-200/60 rounded-2xl bg-white overflow-hidden relative">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-50/80 to-transparent rounded-bl-full opacity-50 z-0"></div>
                
                <div class="p-6 relative z-10 flex-grow">
                    <div class="w-10 h-10 rounded-xl bg-blue-50/80 text-blue-600 flex items-center justify-center mb-4 border border-blue-100/50">
                        @switch($slug)
                            @case('valuation')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @break
                            @case('stock-on-hand')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                @break
                            @case('low-stock')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                @break
                            @case('out-of-stock')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @break
                            @case('aging')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @break
                            @case('warehouse-summary')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                @break
                            @case('transactions')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                @break
                            @case('adjustments')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                @break
                            @case('profitability')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                @break
                        @endswitch
                    </div>
                    
                    <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ $name }}</h3>
                    <p class="text-sm font-medium text-gray-500 line-clamp-2">
                        @switch($slug)
                            @case('valuation')
                                Total financial value of your tracked inventory based on dynamic valuation rules.
                                @break
                            @case('stock-on-hand')
                                Current quantities of all products categorized by warehouse location.
                                @break
                            @case('low-stock')
                                List of items currently sitting at or below their configured reorder minimums.
                                @break
                            @case('out-of-stock')
                                Critical alerts for active products that are completely depleted.
                                @break
                            @case('aging')
                                Estimated age of stock based on historical stock-in transaction dates.
                                @break
                            @case('warehouse-summary')
                                Utilization metrics and estimated valuation breakdown per warehouse.
                                @break
                            @case('transactions')
                                Detailed ledger of all historical stock ins, outs, and manual adjustments.
                                @break
                            @case('adjustments')
                                Log of specific manual quantity adjustments and count variances.
                                @break
                            @case('profitability')
                                Product-level profitability analysis based on estimated COGS vs Sales.
                                @break
                        @endswitch
                    </p>
                </div>
                
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-wrap gap-2 justify-between items-center relative z-10">
                    <a href="{{ route('admin.inventory.reports.show', $slug) }}" class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        View Report <span class="ml-1 text-lg leading-none">&rarr;</span>
                    </a>
                    
                    <div class="flex gap-1.5">
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'pdf']) }}" title="Export PDF" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </a>
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'excel']) }}" title="Export Excel" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </a>
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'csv']) }}" title="Export CSV" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </a>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layouts.admin>
