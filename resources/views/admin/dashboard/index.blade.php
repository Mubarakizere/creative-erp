<x-layouts.admin title="Enterprise Dashboard">
    {{-- Global Header --}}
    <div class="mb-6 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Enterprise Dashboard</h1>
            <p class="mt-1 text-xs text-slate-500 font-medium">
                Welcome back, <strong class="text-slate-900">{{ auth()->user()->first_name }}</strong>!
                @if(auth()->user()->company)
                    <span class="mx-2 text-slate-300">•</span>
                    {{ auth()->user()->company->name }}
                @endif
                @if(auth()->user()->branch)
                    <span class="mx-2 text-slate-300">•</span>
                    {{ auth()->user()->branch->name }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl shadow-xs">
                {{ now()->format('l, F j, Y') }}
            </span>
            @if(isset($fiscalYears) && count($fiscalYears) > 0)
                <span class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl shadow-xs">
                    FY {{ $fiscalYearId ? $fiscalYears->firstWhere('id', $fiscalYearId)?->name : $fiscalYears->first()->name }}
                </span>
            @endif
            <button onclick="window.location.reload()" class="p-2 text-slate-500 hover:text-blue-600 bg-white border border-slate-200 rounded-xl shadow-xs transition-all hover:border-blue-200 hover:bg-blue-50 cursor-pointer" title="Refresh Dashboard">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
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

    {{-- Quick Actions Bar --}}
    <div class="mb-8 overflow-x-auto pb-2 scrollbar-thin">
        <div class="flex gap-3 min-w-max">
            @can('project.create')
            <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Project
            </a>
            @endcan
            @can('invoice.create')
            <a href="{{ route('admin.finance.invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-all">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Invoice
            </a>
            @endcan
            @can('journal.create')
            <a href="{{ route('admin.finance.accounting.journals.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-purple-50 hover:text-purple-600 hover:border-purple-200 transition-all">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Journal
            </a>
            @endcan
            @can('purchase-order.create')
            <a href="{{ route('admin.procurement.pos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-all">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New PO
            </a>
            @endcan
            @can('lead.create')
            <a href="{{ route('admin.crm.leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> New Lead
            </a>
            @endcan
            @can('create', App\Models\ProjectMaterialRequest::class)
            <a href="{{ route('admin.material-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Material Request
            </a>
            @endcan
            @can('time-entry.create')
            <a href="{{ route('admin.time-tracking.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-200 transition-all">
                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Log Time
            </a>
            @endcan
            @can('announcement.create')
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/80 rounded-xl shadow-xs text-xs font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg> Announcement
            </a>
            @endcan
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8" x-data="{ loading: false }" @filters-loading.window="loading = true">
        
        {{-- Main Content Column --}}
        <div class="xl:col-span-8 2xl:col-span-9 space-y-8" x-show="!loading">

            {{-- 1. FINANCE OVERVIEW --}}
            @can('finance.view')
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Financial Overview</h2>
                    <a href="{{ route('admin.finance.analytics') }}" class="text-xs font-bold text-blue-600 hover:underline">Finance Module &rarr;</a>
                </div>
                
                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stats-card title="Total Revenue" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['total_payments']['value'] ?? 0)) }}" color="green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Revenue This Month" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['revenue_this_month']['value'] ?? 0)) }}" color="emerald">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Receivables" value="{{ format_currency((float) str_replace(['$', ','], '', $stats['total_receivables']['value'] ?? 0)) }}" color="indigo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </x-stats-card>
                    <x-stats-card title="Collection Rate" value="{{ $stats['collection_rate']['value'] ?? '0.0%' }}" color="blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </x-stats-card>
                </div>
                
                {{-- Chart & Table Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card class="h-full">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Revenue vs Expenses</h3>
                        <div x-data="{
                            init() {
                                let options = {
                                    chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                                    series: [
                                        { name: 'Revenue', data: {{ json_encode($chartData['revenueTrends'] ?? []) }} }, 
                                        { name: 'Expenses', data: {{ json_encode($chartData['expenseTrends'] ?? []) }} }
                                    ],
                                    colors: ['#10B981', '#EF4444'],
                                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
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
                    
                    <x-card class="h-full" :padding="false">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Outstanding Invoices</h3>
                            <a href="{{ route('admin.finance.invoices.index') }}" class="text-xs font-bold text-blue-600 hover:underline">View All</a>
                        </div>
                        @php
                            $outstandingInvoices = \App\Models\Invoice::with('client')->whereIn('status', ['Sent', 'Overdue', 'Partially Paid'])->orderBy('due_date', 'asc')->take(5)->get();
                        @endphp
                        @if($outstandingInvoices->count() > 0)
                        <x-table>
                            <x-slot:head>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Client</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Amount</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Due Date</th>
                            </x-slot:head>
                            @foreach($outstandingInvoices as $invoice)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-xs font-bold text-slate-900">{{ Str::limit($invoice->client->display_name ?? ($invoice->client->name ?? 'Unknown'), 20) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-slate-900">{{ format_currency($invoice->balance_due ?? 0) }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if($invoice->due_date && $invoice->due_date->isPast())
                                        <span class="text-rose-600 font-bold">{{ $invoice->due_date->format('M d') }}</span>
                                    @else
                                        <span class="text-slate-600 font-medium">{{ $invoice->due_date ? $invoice->due_date->format('M d') : 'N/A' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </x-table>
                        @else
                        <div class="flex flex-col items-center justify-center py-12 text-slate-500 text-xs">
                            <p class="font-medium">No outstanding invoices</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
            @endcan

            {{-- 2. PROJECTS & OPERATIONS --}}
            @can('project.view')
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Projects & Operations</h2>
                    <a href="{{ route('admin.projects.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Projects Module &rarr;</a>
                </div>
                
                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stats-card title="Total Projects" value="{{ number_format($stats['projects'] ?? 0) }}" color="blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="Active Projects" value="{{ number_format($stats['active_projects'] ?? 0) }}" color="emerald">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="My Assigned Tasks" value="{{ number_format($stats['my_tasks'] ?? 0) }}" color="amber">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </x-stats-card>
                    <x-stats-card title="Overdue Tasks" value="{{ number_format($stats['overdue_tasks'] ?? 0) }}" color="rose">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-stats-card>
                </div>

                {{-- Chart & Table Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-card class="h-full">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Task Status Distribution</h3>
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
                                    chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif' },
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
                                <p class="text-xs font-medium text-slate-500">No task data available</p>
                            </div>
                        </div>
                    </x-card>

                    <x-card class="h-full" :padding="false">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Projects</h3>
                            <a href="{{ route('admin.projects.index') }}" class="text-xs font-bold text-blue-600 hover:underline">View All</a>
                        </div>
                        @if(isset($latestProjects) && count($latestProjects) > 0)
                        <x-table>
                            <x-slot:head>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Project</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Progress</th>
                            </x-slot:head>
                            @foreach(collect($latestProjects)->take(5) as $project)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-xs font-bold text-slate-900">
                                    <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-blue-600">{{ Str::limit($project->name, 22) }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge :type="match($project->status) { 'Planning' => 'default', 'In Progress' => 'primary', 'Completed', 'Closed' => 'success', default => 'warning' }">
                                        {{ $project->status }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 min-w-[80px]">
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-[11px] text-slate-600 font-semibold">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </x-table>
                        @else
                        <div class="flex flex-col items-center justify-center py-12 text-slate-500 text-xs">
                            <p class="font-medium">No active projects</p>
                        </div>
                        @endif
                    </x-card>
                </div>
            </div>
            @endcan

        </div>

        {{-- Right Sidebar Column --}}
        <div class="xl:col-span-4 2xl:col-span-3 space-y-6" x-show="!loading">
            {{-- Action Approvals Required --}}
            @can('approval.view')
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-amber-50/40 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Approvals Required
                    </h3>
                    <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ number_format($stats['pending_approvals'] ?? 0) }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @php
                        $pendingApprovals = \App\Models\Approval::with('submitter')->where('status', 'pending')
                            ->whereHas('currentStep', function($q) {
                                $q->where('approver_user_id', auth()->id())
                                  ->orWhereIn('approver_role_id', auth()->user()->roles->pluck('id'));
                            })
                            ->latest()->take(4)->get();
                    @endphp
                    @forelse($pendingApprovals as $approval)
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60 shadow-xs">
                        <p class="text-xs font-bold text-slate-900">{{ class_basename($approval->approvable_type) }} #{{ $approval->approvable_id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Submitted by {{ $approval->submitter->first_name ?? 'System' }}</p>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-500 text-xs">
                        <p class="font-medium">All approvals caught up!</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan

            {{-- Recent Announcements --}}
            @can('announcement.view')
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/40">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Announcements</h3>
                </div>
                @php
                    $announcements = \App\Models\Announcement::latest()->take(4)->get();
                @endphp
                <div class="p-4 space-y-3.5">
                    @forelse($announcements as $announcement)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-xs font-bold text-slate-900 hover:text-blue-600 block truncate">{{ $announcement->title }}</a>
                            <p class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $announcement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-xs">
                        No recent announcements.
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan
        </div>
    </div>
</x-layouts.admin>
