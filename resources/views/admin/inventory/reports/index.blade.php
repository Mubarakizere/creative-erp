<x-layouts.admin title="Inventory Reports">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Inventory Report Center</h1>
        <p class="mt-1 text-sm text-gray-500">Generate, view, and export detailed inventory reports.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($reports as $slug => $name)
            <x-card class="flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">
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
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 justify-between items-center">
                    <a href="{{ route('admin.inventory.reports.show', $slug) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                        View Report &rarr;
                    </a>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'pdf']) }}" title="Export PDF" class="text-gray-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </a>
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'excel']) }}" title="Export Excel" class="text-gray-400 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </a>
                        <a href="{{ route('admin.inventory.reports.show', ['type' => $slug, 'export' => 'csv']) }}" title="Export CSV" class="text-gray-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </a>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layouts.admin>
