<x-layouts.admin title="{{ $title }} - Report">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('admin.inventory.reports.index') }}" class="text-sm text-gray-500 hover:text-gray-900">&larr; Back to Reports</a>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
        </div>
        <div class="flex gap-3">
            <x-button type="default" href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'pdf']) }}">
                Export PDF
            </x-button>
            <x-button type="default" href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'excel']) }}">
                Export Excel
            </x-button>
            <x-button type="default" href="{{ route('admin.inventory.reports.show', ['type' => $type, 'export' => 'csv']) }}">
                Export CSV
            </x-button>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th class="py-3 px-4 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50">
                            @foreach($headers as $header)
                                <td class="py-3 px-4 text-sm {{ str_contains($header, 'Value') || str_contains($header, 'Cost') || str_contains($header, 'Qty') || str_contains($header, 'Quantity') || str_contains($header, 'Profit') || str_contains($header, 'Margin') || str_contains($header, 'Items') ? 'text-right' : '' }} {{ $row[$header] === 'TOTAL' ? 'font-bold' : 'text-gray-700' }}">
                                    {{ $row[$header] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="py-8 text-center text-gray-500">
                                No data available for this report.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
