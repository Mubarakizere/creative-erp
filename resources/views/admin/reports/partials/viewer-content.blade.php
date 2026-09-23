{{-- KPI Cards --}}
@if(!empty($kpis))
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
    @foreach($kpis as $kpiTitle => $kpiValue)
        @php
            $isMoney = Str::contains(strtolower($kpiTitle), ['amount', 'revenue', 'spend', 'invoiced', 'paid', 'valuation', 'value', 'balance', 'cost']);
            $isPositive = Str::contains(strtolower($kpiTitle), ['completed', 'active', 'paid', 'progress', 'approved']);
            $isWarning = Str::contains(strtolower($kpiTitle), ['overdue', 'pending', 'hold', 'low', 'unpaid']);
            $isDanger = Str::contains(strtolower($kpiTitle), ['critical', 'rejected', 'failed', 'cancelled']);

            $colorClasses = match(true) {
                $isDanger => [
                    'icon_bg' => 'bg-rose-100 text-rose-600',
                    'pill' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                    'border' => 'border-rose-100',
                ],
                $isWarning => [
                    'icon_bg' => 'bg-amber-100 text-amber-600',
                    'pill' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                    'border' => 'border-amber-100',
                ],
                $isPositive => [
                    'icon_bg' => 'bg-emerald-100 text-emerald-600',
                    'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'border' => 'border-emerald-100',
                ],
                default => [
                    'icon_bg' => 'bg-blue-100 text-blue-600',
                    'pill' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                    'border' => 'border-slate-100',
                ]
            };

            $value = is_array($kpiValue) ? ($kpiValue['value'] ?? 0) : $kpiValue;
            $trend = is_array($kpiValue) && isset($kpiValue['trend']) ? $kpiValue['trend'] : null;
        @endphp
        <div class="bg-white rounded-2xl p-4 shadow-sm border {{ $colorClasses['border'] }} hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 truncate max-w-[180px]">
                    {{ ucwords(str_replace('_', ' ', $kpiTitle)) }}
                </span>
                <div class="p-2 rounded-xl {{ $colorClasses['icon_bg'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <p class="text-2xl font-bold tracking-tight text-slate-900">
                    @if(is_numeric($value))
                        {{ $isMoney ? 'RWF ' . number_format($value) : number_format($value) }}
                    @else
                        {{ $value }}
                    @endif
                </p>
                @if($trend !== null)
                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full {{ $trend >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                    </span>
                @endif
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

@if(!empty($charts) && $chartType !== 'table' && ($template->type ?? '') !== 'executive')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    @foreach($charts as $chartId => $chartData)
        @if(!empty($chartData) && is_array($chartData))
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        {{ ucwords(str_replace('_', ' ', Str::snake($chartId))) }}
                    </h3>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase">
                        {{ $chartType }}
                    </span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="{{ $chartId }}" 
                        data-chart-widget 
                        data-chart-type="{{ $chartType === 'area' ? 'line' : $chartType }}" 
                        data-chart-labels="{{ json_encode(array_keys($chartData)) }}" 
                        data-chart-values="{{ json_encode(array_values($chartData)) }}"></canvas>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endif

{{-- Data Table --}}
@if(($template->type ?? '') !== 'executive')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <h3 class="text-sm font-semibold text-slate-900">Detailed Dataset</h3>
            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/50">
                {{ count($table_data) }} records
            </span>
        </div>
        <div class="text-xs text-slate-500">
            Auto-formatted preview columns
        </div>
    </div>

    <div class="overflow-x-auto max-h-[550px] custom-scrollbar">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                <tr>
                    @php
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
                            <th scope="col" class="px-5 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap bg-slate-50">
                                @if(str_contains($column, '.'))
                                    {{ ucwords(str_replace('_', ' ', Str::beforeLast($column, '.'))) }} &rsaquo; {{ ucwords(str_replace('_', ' ', Str::afterLast($column, '.'))) }}
                                @else
                                    {{ ucwords(str_replace('_', ' ', $column)) }}
                                @endif
                            </th>
                        @endforeach
                    @else
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Record Details</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($table_data as $row)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        @foreach($selectedColumns as $column)
                            <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                @php
                                    $value = data_get($row, $column);
                                    $colLower = strtolower($column);
                                @endphp

                                @if(is_array($value))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                        Array ({{ count($value) }})
                                    </span>
                                @elseif(is_bool($value))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $value ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $value ? 'Yes' : 'No' }}
                                    </span>
                                @elseif($value === null || $value === '')
                                    <span class="text-slate-300">-</span>
                                @elseif(is_numeric($value) && (str_contains($colLower, 'amount') || str_contains($colLower, 'price') || str_contains($colLower, 'total') || str_contains($colLower, 'revenue') || str_contains($colLower, 'cost') || str_contains($colLower, 'spend') || str_contains($colLower, 'balance')))
                                    <span class="font-semibold text-slate-900">RWF {{ number_format((float)$value) }}</span>
                                @elseif(str_contains($colLower, 'status'))
                                    @php
                                        $valStr = strtolower((string)$value);
                                        $statusClass = match(true) {
                                            in_array($valStr, ['active', 'completed', 'paid', 'approved']) => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
                                            in_array($valStr, ['pending', 'in progress', 'in_progress', 'on hold', 'on_hold', 'draft']) => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                                            in_array($valStr, ['rejected', 'cancelled', 'overdue', 'inactive']) => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
                                            default => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', (string)$value)) }}
                                    </span>
                                @elseif(str_contains($colLower, 'priority'))
                                    @php
                                        $valStr = strtolower((string)$value);
                                        $priorityClass = match(true) {
                                            in_array($valStr, ['critical', 'urgent', 'high']) => 'bg-rose-50 text-rose-700 font-semibold',
                                            in_array($valStr, ['medium']) => 'bg-amber-50 text-amber-700 font-medium',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs {{ $priorityClass }}">
                                        {{ ucfirst((string)$value) }}
                                    </span>
                                @else
                                    <span class="text-slate-800">{{ Str::limit((string)$value, 60) }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900">No matching records found</h4>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm">No data matching your current filters. Try broadening your date range or clearing status criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
