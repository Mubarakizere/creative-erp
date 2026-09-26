<x-layouts.admin title="Reports & Analytics">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $activeFilterCount = 0;
        if ($datePreset && $datePreset !== 'all') $activeFilterCount++;
        if ($dateFrom || $dateTo) $activeFilterCount++;
        if ($selectedCompanyId) $activeFilterCount++;
        if ($projectId) $activeFilterCount++;
        if ($clientId) $activeFilterCount++;
    @endphp

    <div x-data="reportsHub()" x-init="initCharts()">

        {{-- PAGE HEADER --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl ring-1 ring-blue-500/10 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Reports & Analytics</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Real-time business performance metrics, financial KPIs, and reporting catalogs.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-auto">
                @can('create', \App\Models\ReportTemplate::class)
                <a href="{{ route('admin.reports.builder') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xs hover:shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Custom Report Builder</span>
                </a>
                @endcan
            </div>
        </div>

        {{-- STRUCTURED FILTER & QUERY PANEL --}}
        <div class="bg-white rounded-xl border border-gray-200/80 shadow-xs mb-6 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-1.5 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold text-gray-800">Filter Analytics & Reports</span>
                    @if($activeFilterCount > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $activeFilterCount }} {{ Str::plural('filter', $activeFilterCount) }} active
                        </span>
                    @endif
                </div>

                @if($activeFilterCount > 0)
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-medium text-gray-500 hover:text-red-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset All Filters
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {{-- 1. Date Range Preset --}}
                    <div>
                        <label for="filter_date_preset" class="block text-xs font-medium text-gray-600 mb-1.5">Date Preset</label>
                        <select id="filter_date_preset" name="date_preset" x-model="selectedPreset" @change="onPresetChange()"
                                class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_quarter">This Quarter</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Dates</option>
                        </select>
                    </div>

                    {{-- 2. Date From --}}
                    <div>
                        <label for="filter_date_from" class="block text-xs font-medium text-gray-600 mb-1.5">Date From</label>
                        <input type="date" id="filter_date_from" name="date_from" x-model="dateFrom"
                               class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                    </div>

                    {{-- 3. Date To --}}
                    <div>
                        <label for="filter_date_to" class="block text-xs font-medium text-gray-600 mb-1.5">Date To</label>
                        <input type="date" id="filter_date_to" name="date_to" x-model="dateTo"
                               class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                    </div>

                    {{-- 4. Company Selector --}}
                    <div>
                        <label for="filter_company_id" class="block text-xs font-medium text-gray-600 mb-1.5">Company</label>
                        <select id="filter_company_id" name="company_id"
                                class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                            <option value="">All Companies</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}" {{ (string)$selectedCompanyId === (string)$comp->id ? 'selected' : '' }}>
                                    {{ $comp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 5. Project Selector --}}
                    <div>
                        <label for="filter_project_id" class="block text-xs font-medium text-gray-600 mb-1.5">Project</label>
                        <select id="filter_project_id" name="project_id"
                                class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                            <option value="">All Projects</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ (string)$projectId === (string)$proj->id ? 'selected' : '' }}>
                                    {{ $proj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 6. Client Selector --}}
                    <div>
                        <label for="filter_client_id" class="block text-xs font-medium text-gray-600 mb-1.5">Client</label>
                        <select id="filter_client_id" name="client_id"
                                class="w-full text-xs font-medium rounded-lg border-gray-300 bg-white text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-2 shadow-2xs">
                            <option value="">All Clients</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ (string)$clientId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->display_name ?: trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: ($c->company_name ?? 'Client #'.$c->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Submit & Reset actions --}}
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        @if($dateFrom || $dateTo || $selectedCompanyId || $projectId || $clientId || ($datePreset && $datePreset !== 'all'))
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Active parameters:</span>
                            @if($dateFrom || $dateTo)
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-mono text-[11px]">
                                    {{ $dateFrom ? $dateFrom->format('d M Y') : 'Start' }} → {{ $dateTo ? $dateTo->format('d M Y') : 'End' }}
                                </span>
                            @elseif($datePreset && $datePreset !== 'all')
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded capitalize text-[11px]">
                                    {{ str_replace('_', ' ', $datePreset) }}
                                </span>
                            @endif
                            @if($selectedCompanyId && $companies->where('id', $selectedCompanyId)->first())
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[11px]">
                                    Company: {{ $companies->where('id', $selectedCompanyId)->first()->name }}
                                </span>
                            @endif
                            @if($projectId && $projects->where('id', $projectId)->first())
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[11px]">
                                    Project: {{ $projects->where('id', $projectId)->first()->name }}
                                </span>
                            @endif
                            @if($clientId && $clients->where('id', $clientId)->first())
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[11px]">
                                    Client: {{ $clients->where('id', $clientId)->first()->display_name }}
                                </span>
                            @endif
                        @else
                            <span class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                            <span>Showing all available historical records. Adjust criteria above to scope metrics.</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2.5">
                        @if($activeFilterCount > 0)
                            <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                Reset
                            </a>
                        @endif
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-2xs hover:shadow-xs transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Apply Filters</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- EXECUTIVE KPI METRICS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- KPI 1: Total Invoiced --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Invoiced</span>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">RWF {{ number_format($totalInvoiced) }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $invoiceCount }} {{ Str::plural('invoice', $invoiceCount) }}</span>
                    <span>Avg: RWF {{ number_format($avgInvoice) }}</span>
                </div>
            </div>

            {{-- KPI 2: Total Received --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Collected</span>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">RWF {{ number_format($totalPaid) }}</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs">
                    @if($revenueGrowth !== null)
                        @if($revenueGrowth >= 0)
                            <span class="font-medium text-emerald-600 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                +{{ $revenueGrowth }}%
                            </span>
                        @else
                            <span class="font-medium text-red-600 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $revenueGrowth }}%
                            </span>
                        @endif
                        <span class="text-gray-400">vs prev period</span>
                    @else
                        <span class="text-gray-500">Recorded payments</span>
                    @endif
                </div>
            </div>

            {{-- KPI 3: Outstanding Receivables --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Outstanding Balance</span>
                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">RWF {{ number_format($totalOutstanding) }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                    <span>Due from clients</span>
                    @if($overdueCount > 0)
                        <span class="text-amber-600 font-semibold">{{ $overdueCount }} overdue</span>
                    @else
                        <span class="text-emerald-600">0 overdue</span>
                    @endif
                </div>
            </div>

            {{-- KPI 4: Collection Rate --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Collection Efficiency</span>
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">{{ $collectionRate }}%</p>
                <div class="mt-2.5">
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $collectionRate)) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ANALYTICS CHARTS SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            {{-- Trend Chart (2 columns) --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Revenue & Inflow Trends</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Monthly payment collections over the selected timeframe</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-200/60">
                        Monthly
                    </span>
                </div>
                <div class="relative h-64">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            {{-- Invoice Status Distribution (1 column) --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Invoice Status</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Distribution by total billing amount</p>
                        </div>
                    </div>
                    <div class="relative h-44 flex items-center justify-center mb-4">
                        <canvas id="invoiceStatusChart"></canvas>
                    </div>
                </div>

                <div class="space-y-2 border-t border-gray-100 pt-3">
                    @forelse($invoiceStatusData as $row)
                        @php
                            $colors = [
                                'paid' => 'bg-emerald-500',
                                'overdue' => 'bg-red-500',
                                'sent' => 'bg-blue-500',
                                'draft' => 'bg-gray-400',
                                'partially paid' => 'bg-amber-500',
                                'cancelled' => 'bg-slate-400',
                            ];
                            $dotColor = $colors[strtolower($row->status)] ?? 'bg-indigo-500';
                        @endphp
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                                <span class="text-gray-700 capitalize font-medium">{{ $row->status }}</span>
                                <span class="text-gray-400 text-[11px]">({{ $row->count }})</span>
                            </div>
                            <span class="text-gray-900 font-semibold font-mono">RWF {{ number_format($row->total) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">No invoices recorded in this scope</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CHARTS ROW 2: Payment Methods & Top Clients --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
            {{-- Payment Methods --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Payment Channels</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Revenue breakdown by payment method</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative h-44 w-44 shrink-0">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="flex-1 w-full space-y-2.5">
                        @forelse($paymentMethodData as $pm)
                            <div class="flex items-center justify-between text-xs p-2 rounded-lg bg-gray-50/60">
                                <span class="font-medium text-gray-700">{{ $pm->paymentMethod ? $pm->paymentMethod->name : 'Direct / Bank' }}</span>
                                <span class="font-semibold text-gray-900 font-mono">RWF {{ number_format($pm->total) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-4 text-center">No payment data in this scope</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Top Clients by Revenue --}}
            <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Top Revenue Clients</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Top invoiced clients within selected parameters</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                        Top 5
                    </span>
                </div>

                @php $maxClientTotal = $topClients->max('total') ?: 1; @endphp
                <div class="space-y-3.5">
                    @forelse($topClients as $idx => $tc)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-gray-100 text-gray-600 font-semibold text-[11px] flex items-center justify-center shrink-0">
                                        {{ $idx + 1 }}
                                    </span>
                                    <span class="font-semibold text-gray-800 truncate">
                                        {{ $tc->client ? ($tc->client->display_name ?: trim(($tc->client->first_name ?? '') . ' ' . ($tc->client->last_name ?? ''))) : 'Client #'.$tc->client_id }}
                                    </span>
                                    <span class="text-gray-400 text-[11px] shrink-0">({{ $tc->invoice_count }} inv)</span>
                                </div>
                                <span class="font-bold text-gray-900 font-mono ml-2 shrink-0">
                                    RWF {{ number_format($tc->total) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ round(($tc->total / $maxClientTotal) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-6 text-center">No client billing activity found for this criteria</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- REPORT CATALOG & TEMPLATES SECTION --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Report Catalog</h2>
                    <p class="text-xs text-gray-500">Run standardized or custom report templates with your scoped parameters.</p>
                </div>

                {{-- Template search & tab filters --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="relative">
                        <input type="text" x-model="templateSearch" placeholder="Find report template..."
                               class="text-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8 pr-3 py-1.5 w-48 sm:w-56 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="inline-flex p-0.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold">
                        <button type="button" @click="activeTab = 'all'"
                                :class="activeTab === 'all' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1 rounded-md transition-all">
                            All ({{ $systemTemplates->count() + $userTemplates->count() }})
                        </button>
                        <button type="button" @click="activeTab = 'standard'"
                                :class="activeTab === 'standard' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1 rounded-md transition-all">
                            Standard ({{ $systemTemplates->count() }})
                        </button>
                        @if($userTemplates->count() > 0)
                        <button type="button" @click="activeTab = 'custom'"
                                :class="activeTab === 'custom' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1 rounded-md transition-all">
                            Custom ({{ $userTemplates->count() }})
                        </button>
                        @endif
                        @if($favoriteTemplates->count() > 0)
                        <button type="button" @click="activeTab = 'favorites'"
                                :class="activeTab === 'favorites' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1 rounded-md transition-all">
                            Favorites ({{ $favoriteTemplates->count() }})
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Templates Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @php
                    $allTemplates = $systemTemplates->merge($userTemplates);
                @endphp

                @foreach($allTemplates as $template)
                    @php
                        $isFav = $favoriteTemplates->contains('id', $template->id);
                        $isCustom = !$template->is_system;
                    @endphp
                    <div x-show="matchesFilter('{{ strtolower(addslashes($template->name)) }}', '{{ strtolower(addslashes($template->description ?? '')) }}', {{ $isCustom ? 'true' : 'false' }}, {{ $isFav ? 'true' : 'false' }})"
                         class="bg-white rounded-xl border border-gray-200/80 hover:border-blue-400/80 p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="w-9 h-9 rounded-lg {{ $isCustom ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }} flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($isCustom)
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-purple-700 bg-purple-50 px-2 py-0.5 rounded">Custom</span>
                                    @else
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-700 bg-blue-50 px-2 py-0.5 rounded">Standard</span>
                                    @endif

                                    <form action="{{ route('admin.reports.favorite', $template) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="{{ $isFav ? 'Remove favorite' : 'Add favorite' }}"
                                                class="p-1 rounded text-gray-300 hover:text-amber-500 transition-colors">
                                            <svg class="w-4 h-4 {{ $isFav ? 'text-amber-500 fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $template->name }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $template->description ?: 'Pre-configured analytical query and presentation template.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <a href="{{ route('admin.reports.show', $template) }}"
                               class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 group/btn">
                                <span>Run Report</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>

                            @if($isCustom)
                                <button type="button" @click="$dispatch('open-modal', 'del-{{ $template->id }}')"
                                        class="text-gray-400 hover:text-red-500 p-1 rounded transition-colors" title="Delete template">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>

                                <x-modal id="del-{{ $template->id }}" maxWidth="md">
                                    <x-slot:header>Delete Report Template</x-slot:header>
                                    <div class="text-center py-4">
                                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Delete {{ $template->name }}?</h3>
                                        <p class="text-xs text-gray-500">This custom template will be permanently removed.</p>
                                    </div>
                                    <x-slot:footer>
                                        <div class="flex items-center gap-2 justify-end">
                                            <button type="button" @click="open=false" class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                                            <form action="{{ route('admin.reports.destroy', $template) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                                            </form>
                                        </div>
                                    </x-slot:footer>
                                </x-modal>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <script>
    function reportsHub() {
        return {
            selectedPreset: '{{ $datePreset ?? "all" }}',
            dateFrom: '{{ $dateFrom ? $dateFrom->format("Y-m-d") : "" }}',
            dateTo: '{{ $dateTo ? $dateTo->format("Y-m-d") : "" }}',
            templateSearch: '',
            activeTab: 'all',

            onPresetChange() {
                const now = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

                if (this.selectedPreset === 'today') {
                    this.dateFrom = formatDate(now);
                    this.dateTo = formatDate(now);
                } else if (this.selectedPreset === 'yesterday') {
                    const y = new Date(now);
                    y.setDate(y.getDate() - 1);
                    this.dateFrom = formatDate(y);
                    this.dateTo = formatDate(y);
                } else if (this.selectedPreset === 'this_week') {
                    const first = new Date(now);
                    const day = first.getDay() || 7;
                    first.setDate(first.getDate() - day + 1);
                    const last = new Date(first);
                    last.setDate(last.getDate() + 6);
                    this.dateFrom = formatDate(first);
                    this.dateTo = formatDate(last);
                } else if (this.selectedPreset === 'this_month') {
                    const first = new Date(now.getFullYear(), now.getMonth(), 1);
                    const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                    this.dateFrom = formatDate(first);
                    this.dateTo = formatDate(last);
                } else if (this.selectedPreset === 'last_month') {
                    const first = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    const last = new Date(now.getFullYear(), now.getMonth(), 0);
                    this.dateFrom = formatDate(first);
                    this.dateTo = formatDate(last);
                } else if (this.selectedPreset === 'this_quarter') {
                    const quarter = Math.floor(now.getMonth() / 3);
                    const first = new Date(now.getFullYear(), quarter * 3, 1);
                    const last = new Date(now.getFullYear(), quarter * 3 + 3, 0);
                    this.dateFrom = formatDate(first);
                    this.dateTo = formatDate(last);
                } else if (this.selectedPreset === 'this_year') {
                    this.dateFrom = `${now.getFullYear()}-01-01`;
                    this.dateTo = `${now.getFullYear()}-12-31`;
                } else if (this.selectedPreset === 'all') {
                    this.dateFrom = '';
                    this.dateTo = '';
                }
            },

            matchesFilter(name, desc, isCustom, isFav) {
                if (this.activeTab === 'standard' && isCustom) return false;
                if (this.activeTab === 'custom' && !isCustom) return false;
                if (this.activeTab === 'favorites' && !isFav) return false;

                if (!this.templateSearch.trim()) return true;
                const q = this.templateSearch.toLowerCase();
                return name.includes(q) || desc.includes(q);
            },

            initCharts() {
                Chart.defaults.font.family = "'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif";
                Chart.defaults.color = '#64748b';
                Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a';
                Chart.defaults.plugins.tooltip.padding = 10;
                Chart.defaults.plugins.tooltip.cornerRadius = 8;

                this.$nextTick(() => {
                    this.renderRevenueTrend();
                    this.renderInvoiceStatus();
                    this.renderPaymentMethods();
                });
            },

            renderRevenueTrend() {
                const el = document.getElementById('revenueTrendChart');
                if (!el) return;
                const ctx = el.getContext('2d');
                const trendData = @json($monthlyPayments);

                const grad = ctx.createLinearGradient(0, 0, 0, 240);
                grad.addColorStop(0, 'rgba(37, 99, 235, 0.14)');
                grad.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(item => item.label),
                        datasets: [{
                            label: 'Collected Revenue (RWF)',
                            data: trendData.map(item => item.total),
                            borderColor: '#2563eb',
                            backgroundColor: grad,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => 'RWF ' + Number(ctx.raw || 0).toLocaleString()
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    callback: v => v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : v >= 1e3 ? (v / 1e3).toFixed(0) + 'k' : v
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            },

            renderInvoiceStatus() {
                const el = document.getElementById('invoiceStatusChart');
                if (!el) return;
                const ctx = el.getContext('2d');
                const raw = @json($invoiceStatusData);

                const colorMap = {
                    paid: '#10b981',
                    overdue: '#ef4444',
                    sent: '#3b82f6',
                    draft: '#94a3b8',
                    'partially paid': '#f59e0b',
                    cancelled: '#64748b'
                };

                const labels = raw.map(d => d.status);
                const data = raw.map(d => d.total);
                const colors = labels.map(l => colorMap[l.toLowerCase()] || '#6366f1');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data.length > 0 ? data : [1],
                            backgroundColor: data.length > 0 ? colors : ['#e2e8f0'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => data.length > 0 ? ' RWF ' + Number(ctx.raw || 0).toLocaleString() : ' No data'
                                }
                            }
                        }
                    }
                });
            },

            renderPaymentMethods() {
                const el = document.getElementById('paymentMethodsChart');
                if (!el) return;
                const ctx = el.getContext('2d');
                const raw = @json($paymentMethodData);

                const labels = raw.map(d => d.payment_method ? d.payment_method.name : 'Direct / Bank');
                const data = raw.map(d => d.total);
                const palette = ['#2563eb', '#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#64748b'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data.length > 0 ? data : [1],
                            backgroundColor: data.length > 0 ? palette.slice(0, data.length) : ['#e2e8f0'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => data.length > 0 ? ' RWF ' + Number(ctx.raw || 0).toLocaleString() : ' No data'
                                }
                            }
                        }
                    }
                });
            }
        };
    }
    </script>
</x-layouts.admin>
