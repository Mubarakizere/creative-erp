{{-- KPI Cards --}}
@if(!empty($kpis))
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    @foreach($kpis as $kpiTitle => $kpiValue)
        @php
            $color = match(true) {
                str_contains($kpiTitle, 'total') => 'blue',
                str_contains($kpiTitle, 'active') => 'emerald',
                str_contains($kpiTitle, 'completed') => 'indigo',
                str_contains($kpiTitle, 'overdue') => 'rose',
                default => 'purple'
            };
            // Support vs Last Period objects if they exist
            $value = is_array($kpiValue) ? $kpiValue['value'] : $kpiValue;
            $trend = is_array($kpiValue) && isset($kpiValue['trend']) ? $kpiValue['trend'] : null;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ ucwords(str_replace('_', ' ', $kpiTitle)) }}</p>
                <div class="flex items-baseline">
                    <p class="text-2xl font-bold text-gray-900">{{ is_numeric($value) ? number_format($value) : $value }}</p>
                    @if($trend)
                        <span class="ml-2 text-sm font-medium {{ $trend > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $trend > 0 ? '+' : '' }}{{ $trend }}% vs last period
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- Charts --}}
@php
    $layout = $template->layout ?? [];
    $chartType = $layout['chartType'] ?? 'table';
@endphp

@if(!empty($charts) && $chartType !== 'table' && $template->type !== 'executive')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    @foreach($charts as $chartId => $chartData)
        @if(!empty($chartData) && is_array($chartData))
            <x-reports.chart-widget 
                :id="$chartId" 
                :title="ucwords(str_replace('_', ' ', Str::snake($chartId)))" 
                :type="$chartType === 'area' ? 'line' : $chartType" 
                :labels="array_keys($chartData)" 
                :data="array_values($chartData)" 
            />
        @endif
    @endforeach
</div>
@endif

{{-- Data Table --}}
@if($template->type !== 'executive')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Detailed Records</h3>
        <span class="text-sm text-gray-500">{{ count($table_data) }} records found</span>
    </div>
    <div class="overflow-x-auto max-h-[600px] custom-scrollbar">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                <tr>
                    @php
                        // If specific columns selected, use them, otherwise use all available
                        $selectedColumns = $layout['columns'] ?? [];
                        if (empty($selectedColumns) && $table_data->count() > 0) {
                            $sample = $table_data->first()->toArray();
                            $selectedColumns = array_filter(array_keys($sample), function($key) {
                                return !is_array($key) && !in_array($key, ['deleted_at', 'password', 'remember_token']);
                            });
                        }
                    @endphp
                    
                    @if(!empty($selectedColumns))
                        @foreach($selectedColumns as $column)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if(str_contains($column, '.'))
                                    {{ str_replace('_', ' ', Str::beforeLast($column, '.')) }}
                                @else
                                    {{ str_replace('_', ' ', $column) }}
                                @endif
                            </th>
                        @endforeach
                    @else
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($table_data as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        @php
                            $rowData = $row->toArray();
                        @endphp
                        @foreach($selectedColumns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @php
                                    // Handle nested properties like client.name
                                    $value = data_get($row, $column);
                                @endphp

                                @if(is_array($value))
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Array</span>
                                @elseif(is_bool($value))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $value ? 'Yes' : 'No' }}
                                    </span>
                                @else
                                    {{ Str::limit($value ?? '-', 50) }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="px-6 py-8 text-center text-sm text-gray-500">
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No records found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
