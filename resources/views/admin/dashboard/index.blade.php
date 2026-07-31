<x-layouts.admin title="Enterprise Dashboard">
    {{-- Global Header --}}
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Enterprise Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">
                Welcome back, {{ auth()->user()->first_name }}!
                @if(auth()->user()->company)
                    <span class="mx-2 text-gray-300">|</span>
                    {{ auth()->user()->company->name }}
                @endif
                @if(auth()->user()->branch)
                    <span class="mx-2 text-gray-300">|</span>
                    {{ auth()->user()->branch->name }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm">
                {{ now()->format('l, F j, Y') }}
            </span>
            @if(isset($fiscalYears) && count($fiscalYears) > 0)
                <span class="text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm">
                    FY {{ $fiscalYearId ? $fiscalYears->firstWhere('id', $fiscalYearId)?->name : $fiscalYears->first()->name }}
                </span>
            @endif
            <button onclick="window.location.reload()" class="p-2 text-gray-500 hover:text-blue-600 bg-white border border-gray-200 rounded-lg shadow-sm transition-all hover:border-blue-200 hover:bg-blue-50" title="Refresh Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    @if(isset($filters))
        <div class="mb-6">
            <x-financial-filter-bar 
                action="{{ route('admin.dashboard') }}"
                :fiscalYears="$fiscalYears ?? []"
                :fiscalYearId="$fiscalYearId ?? null"
                :branches="$branches ?? []"
                :departments="$departments ?? []"
                :projects="$projects ?? []"
                :clients="$clients ?? []"
                :filters="$filters"
            />
        </div>
    @endif

    {{-- Quick Actions --}}
    <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <div class="flex gap-3 min-w-max">
            @can('project.create')
            <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Project
            </a>
            @endcan
            @can('invoice.create')
            <a href="{{ route('admin.finance.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-emerald-600 hover:border-emerald-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Invoice
            </a>
            @endcan
            @can('journal.create')
            <a href="{{ route('admin.finance.accounting.journals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-purple-600 hover:border-purple-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Journal
            </a>
            @endcan
            @can('purchase-order.create')
            <a href="{{ route('admin.procurement.pos.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-orange-600 hover:border-orange-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New PO
            </a>
            @endcan
            @can('lead.create')
            <a href="{{ route('admin.crm.leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Lead
            </a>
            @endcan
            @can('project-material-request.create')
            <a href="{{ route('admin.material-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-amber-600 hover:border-amber-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Material Request
            </a>
            @endcan
            @can('time-entry.create')
            <a href="{{ route('admin.time-tracking.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-cyan-600 hover:border-cyan-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Log Time
            </a>
            @endcan
            @can('announcement.create')
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-rose-600 hover:border-rose-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg> Post Announcement
            </a>
            @endcan
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8" x-data="{ loading: false }" @filters-loading.window="loading = true">
        
        {{-- Skeleton Loading State --}}
        <div class="xl:col-span-12 w-full" x-show="loading" style="display: none;">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <div class="xl:col-span-8 2xl:col-span-9 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <x-skeletons.card /><x-skeletons.card /><x-skeletons.card /><x-skeletons.card />
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <x-skeletons.chart />
                        <x-skeletons.chart />
                    </div>
                    <x-skeletons.table :rows="5" :cols="6" />
                </div>
                <div class="xl:col-span-4 2xl:col-span-3 space-y-8">
                    <x-skeletons.widget />
                    <x-skeletons.widget />
                    <x-skeletons.list />
                </div>
            </div>
        </div>

        {{-- Main Content Column --}}
        <div class="xl:col-span-8 2xl:col-span-9 space-y-10" x-show="!loading">

            {{-- 1. FINANCE MODULE --}}
            @can('finance.view')
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Financial Overview</h2>
                    <a href="{{ route('admin.finance.analytics') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Finance Module &rarr;</a>
                </div>
                
                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stats-card title="Total Revenue" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['total_payments']['value'] ?? 0)) }}" color="green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Revenue This Month" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['revenue_this_month']['value'] ?? 0)) }}" color="emerald">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Outstanding Receivables" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['total_receivables']['value'] ?? 0)) }}" color="indigo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Collection Rate" value="{{ $stats['collection_rate']['value'] ?? '0.0%' }}" color="blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </x-stats-card>
                </div>
                
                {{-- Chart & Table Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card class="h-full">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue vs Expenses</h3>
                        <div x-data="{
                            init() {
                                let options = {
                                    chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                                    series: [
                                        { name: 'Revenue', data: {{ json_encode($chartData['revenueTrends'] ?? []) }} }, 
                                        { name: 'Expenses', data: {{ json_encode($chartData['expenseTrends'] ?? []) }} }
                                    ],
                                    colors: ['#10B981', '#EF4444'],
                                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] } },
                                    dataLabels: { enabled: false },
                                    stroke: { curve: 'smooth', width: 2 },
                                    xaxis: { categories: ['M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Current'] }
                                };
                                new ApexCharts(this.$refs.chart, options).render();
                            }
                        }">
                            <div x-ref="chart"></div>
                        </div>
                    </x-card>
                    
                    <x-card class="h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Outstanding Invoices</h3>
                            <a href="{{ route('admin.finance.invoices.index') }}" class="text-xs font-medium text-blue-600 hover:underline">View All</a>
                        </div>
                        @php
                            // Fetch only if needed to avoid duplicate logic, using with() for performance
                            $outstandingInvoices = \App\Models\Invoice::with('client')->whereIn('status', ['Sent', 'Overdue', 'Partially Paid'])->orderBy('due_date', 'asc')->take(5)->get();
                        @endphp
                        @if($outstandingInvoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Due</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50">
                                    @foreach($outstandingInvoices as $invoice)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-900">{{ Str::limit($invoice->client->name ?? 'Unknown', 20) }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900">{{ format_currency($invoice->balance_due ?? 0) }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-500">
                                            @if($invoice->due_date && $invoice->due_date->isPast())
                                                <span class="text-rose-600 font-medium">{{ $invoice->due_date->format('M d') }}</span>
                                            @else
                                                {{ $invoice->due_date ? $invoice->due_date->format('M d') : 'N/A' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-10">
                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium text-gray-500">No outstanding invoices</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
            @endcan

            {{-- 2. PROJECTS MODULE --}}
            @can('project.view')
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Projects & Operations</h2>
                    <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Project Module &rarr;</a>
                </div>
                
                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stats-card title="Total Projects" value="{{ number_format($stats['projects'] ?? 0) }}" color="blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="Active Projects" value="{{ number_format($stats['active_projects'] ?? 0) }}" color="emerald">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="My Tasks" value="{{ number_format($stats['my_tasks'] ?? 0) }}" color="amber">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="Overdue Tasks" value="{{ number_format($stats['overdue_tasks'] ?? 0) }}" color="rose">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-stats-card>
                </div>

                {{-- Chart & Table Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card class="h-full">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Task Status Distribution</h3>
                        <div x-data="{
                            init() {
                                let taskData = {{ json_encode($chartData['tasksByStatus'] ?? []) }};
                                let labels = Object.keys(taskData);
                                let series = Object.values(taskData);
                                
                                if(series.length === 0 || series.every(item => item === 0)) {
                                    this.$refs.empty.classList.remove('hidden');
                                    return;
                                }

                                let options = {
                                    chart: { type: 'donut', height: 300, fontFamily: 'Inter, sans-serif' },
                                    series: series,
                                    labels: labels,
                                    colors: ['#6B7280', '#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                                    dataLabels: { enabled: false },
                                    plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Total Tasks' } } } } },
                                    legend: { position: 'bottom' },
                                    stroke: { width: 0 }
                                };
                                new ApexCharts(this.$refs.chart, options).render();
                            }
                        }">
                            <div x-ref="chart"></div>
                            <div x-ref="empty" class="hidden flex flex-col items-center justify-center py-10">
                                <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <p class="text-sm font-medium text-gray-500">No task data available</p>
                            </div>
                        </div>
                    </x-card>

                    <x-card class="h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Latest Projects</h3>
                            <a href="{{ route('admin.projects.index') }}" class="text-xs font-medium text-blue-600 hover:underline">View All</a>
                        </div>
                        @if(isset($latestProjects) && count($latestProjects) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Project</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Progress</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50">
                                    @foreach(collect($latestProjects)->take(5) as $project)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-blue-600">{{ Str::limit($project->name, 25) }}</a>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <x-badge :type="match($project->status) { 'Planning' => 'default', 'In Progress' => 'primary', 'Completed', 'Closed' => 'success', default => 'warning' }" class="text-[10px] px-2 py-0.5">
                                                {{ $project->status }}
                                            </x-badge>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-full bg-gray-100 rounded-full h-1.5 w-16 overflow-hidden">
                                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500 font-medium">{{ $project->progress ?? 0 }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-10">
                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="text-sm font-medium text-gray-500">No active projects</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
            @endcan

            {{-- 3. CRM MODULE --}}
            @canany(['lead.view', 'opportunity.view'])
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">CRM & Sales</h2>
                    <a href="{{ route('admin.crm.opportunities.kanban') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">CRM Module &rarr;</a>
                </div>
                
                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stats-card title="Total Leads" value="{{ number_format($stats['total_leads'] ?? 0) }}" color="blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Active Deals" value="{{ number_format($stats['total_opportunities'] ?? 0) }}" color="indigo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Pipeline Value" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['pipeline_value'] ?? 0)) }}" color="emerald">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Conversion Rate" value="{{ $stats['conversion_rate'] ?? 0 }}%" color="purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l10-16M6 9h.01M18 15h.01"/></svg>
                    </x-stats-card>
                </div>
            </div>
            @endcan

            {{-- 4. SUPPLY CHAIN & PROCUREMENT MODULE --}}
            @canany(['purchase-order.view', 'inventory.view'])
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Procurement & Inventory</h2>
                    <a href="{{ route('admin.procurement.requisitions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Supply Chain &rarr;</a>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card class="h-full">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Procurement Spend</h3>
                        <div x-data="{
                            init() {
                                let options = {
                                    chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                                    series: [{ name: 'Spend', data: {{ json_encode($chartData['purchaseTrends'] ?? []) }} }],
                                    colors: ['#F97316'],
                                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                                    dataLabels: { enabled: false },
                                    grid: { strokeDashArray: 4 },
                                    xaxis: { categories: ['M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Current'] }
                                };
                                new ApexCharts(this.$refs.chart, options).render();
                            }
                        }">
                            <div x-ref="chart"></div>
                        </div>
                    </x-card>
                    
                    <x-card class="h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Pending Purchase Orders</h3>
                            <a href="{{ route('admin.procurement.pos.index') }}" class="text-xs font-medium text-blue-600 hover:underline">View All</a>
                        </div>
                        @php
                            $pendingPos = \App\Models\PurchaseOrder::with('supplier')->whereIn('status', ['pending', 'approved'])->latest()->take(5)->get();
                        @endphp
                        @if($pendingPos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">PO #</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Supplier</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50">
                                    @foreach($pendingPos as $po)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <a href="{{ route('admin.procurement.pos.show', $po) }}" class="hover:underline">{{ $po->po_number }}</a>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-900">{{ Str::limit($po->supplier->name ?? 'Unknown', 20) }}</td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900">{{ format_currency($po->grand_total ?? 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-10">
                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-sm font-medium text-gray-500">No pending purchase orders</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
            @endcan

        </div>

        {{-- Right Sidebar Column (Sticky) --}}
        <div class="xl:col-span-4 2xl:col-span-3 relative" x-show="!loading">
            <div class="sticky top-6 space-y-6">
                
                {{-- Pending Approvals --}}
                @can('approval.view')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-amber-50/30 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Action Required
                        </h3>
                        <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ number_format($stats['pending_approvals'] ?? 0) }}</span>
                    </div>
                    <div class="p-4 space-y-3">
                        @php
                            $pendingApprovals = \App\Models\Approval::with('requester')->where('status', 'pending')
                                ->where('approver_id', auth()->id())
                                ->latest()->take(4)->get();
                        @endphp
                        @forelse($pendingApprovals as $approval)
                        <div class="p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:border-amber-200 hover:shadow-md transition-all">
                            <p class="text-sm font-semibold text-gray-900">{{ class_basename($approval->approvable_type) }} #{{ $approval->approvable_id }}</p>
                            <p class="text-xs text-gray-500 mt-1">Req. by {{ $approval->requester->first_name ?? 'System' }}</p>
                            <div class="mt-3 flex gap-2">
                                <a href="#" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded hover:bg-emerald-100 transition-colors w-full text-center">Approve</a>
                                <a href="#" class="px-3 py-1.5 bg-rose-50 text-rose-700 text-xs font-semibold rounded hover:bg-rose-100 transition-colors w-full text-center">Reject</a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            <p class="text-sm font-medium text-gray-500">You're all caught up!</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endcan

                {{-- Today's Schedule & Meetings --}}
                @can('calendar.view')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Today's Schedule</h3>
                        <a href="{{ route('admin.calendar.agenda') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Agenda &rarr;</a>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($todaysSchedule ?? [] as $event)
                            <a href="{{ $event->url }}" class="block p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors shadow-sm" style="border-left: 3px solid {{ $event->color ?? '#3B82F6' }};">
                                <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $event->title }}</h4>
                                <p class="text-xs font-medium text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @if(!($event->allDay ?? false))
                                        {{ $event->start->format('g:i A') }} — {{ $event->end?->format('g:i A') }}
                                    @else
                                        All Day
                                    @endif
                                </p>
                            </a>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-medium text-gray-500">Clear calendar today.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endcan

                {{-- Recent Announcements --}}
                @can('announcement.view')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900">Announcements</h3>
                    </div>
                    @php
                        $announcements = \App\Models\Announcement::latest()->take(4)->get();
                    @endphp
                    <div class="p-4 space-y-4">
                        @forelse($announcements as $announcement)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 block truncate">{{ $announcement->title }}</a>
                                <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $announcement->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-sm font-medium text-gray-500">No recent announcements.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endcan

            </div>
        </div>
    </div>
</x-layouts.admin>
