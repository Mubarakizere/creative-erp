<x-layouts.admin title="{{ $title }} - Report">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('admin.inventory.reports.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Reports
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $title }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm focus:ring-2 focus:ring-red-500 focus:outline-none min-h-[42px]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
            </a>
            <a href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'excel']) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-xl hover:bg-green-100 transition-colors shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none min-h-[42px]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <a href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'csv']) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm focus:ring-2 focus:ring-gray-500 focus:outline-none min-h-[42px]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        @foreach($headers as $header)
                            <th class="py-3 px-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 {{ str_contains($header, 'Value') || str_contains($header, 'Cost') || str_contains($header, 'Qty') || str_contains($header, 'Quantity') || str_contains($header, 'Profit') || str_contains($header, 'Margin') || str_contains($header, 'Items') ? 'text-right' : '' }}">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            @foreach($headers as $header)
                                <td class="py-4 px-6 text-sm {{ str_contains($header, 'Value') || str_contains($header, 'Cost') || str_contains($header, 'Qty') || str_contains($header, 'Quantity') || str_contains($header, 'Profit') || str_contains($header, 'Margin') || str_contains($header, 'Items') ? 'text-right' : '' }} {{ $row[$header] === 'TOTAL' ? 'font-black text-gray-900 tracking-tight text-base' : 'font-medium text-gray-700' }}">
                                    {{ $row[$header] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="py-12 px-6 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-gray-100">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">No data available for this report.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
