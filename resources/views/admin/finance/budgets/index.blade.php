<x-layouts.admin title="Project Budgets">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Project Budgets'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="p-2.5 bg-indigo-600/10 rounded-xl text-indigo-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Project Budgets</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Manage activity-level budgets, task fund allocations, and financial variance per project.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
                   class="inline-flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-2xs hover:shadow-xs">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>

                @can('create', App\Models\Budget::class)
                    <a href="{{ route('admin.finance.budgets.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs hover:shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Project Budget
                    </a>
                @endcan
            </div>
        </div>

        {{-- Executive KPI Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stats-card 
                title="Total Budgets" 
                :value="number_format($stats['total_count'] ?? 0)" 
                color="blue"
            >
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="Total Allocated Capital" 
                :value="format_currency($stats['total_amount'] ?? 0, 'RWF')" 
                color="emerald"
            >
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="Active & Approved" 
                :value="number_format(($stats['active_count'] ?? 0) + ($stats['approved_count'] ?? 0))" 
                color="indigo"
            >
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-stats-card>

            <x-stats-card 
                title="Funded Activities" 
                :value="number_format($stats['total_activities'] ?? 0)" 
                color="purple"
            >
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </x-stats-card>
        </div>

        {{-- Status Quick Filter Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            @php
                $currentStatus = request('status');
                $tabs = [
                    ['key' => '', 'label' => 'All Budgets', 'count' => $stats['total_count'] ?? 0, 'dot' => 'bg-slate-400'],
                    ['key' => 'active', 'label' => 'Active', 'count' => $stats['active_count'] ?? 0, 'dot' => 'bg-emerald-500'],
                    ['key' => 'approved', 'label' => 'Approved', 'count' => $stats['approved_count'] ?? 0, 'dot' => 'bg-blue-500'],
                    ['key' => 'draft', 'label' => 'Draft', 'count' => $stats['draft_count'] ?? 0, 'dot' => 'bg-amber-500'],
                    ['key' => 'closed', 'label' => 'Closed', 'count' => $stats['closed_count'] ?? 0, 'dot' => 'bg-slate-500'],
                ];
            @endphp

            @foreach($tabs as $tab)
                @php
                    $isActive = ($tab['key'] === '' && !$currentStatus) || ($currentStatus === $tab['key']);
                    $tabUrl = request()->fullUrlWithQuery(['status' => $tab['key'] ?: null, 'page' => null]);
                @endphp
                <a href="{{ $tabUrl }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl font-bold transition-all whitespace-nowrap {{ $isActive ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50' }}">
                    <span class="w-2 h-2 rounded-full {{ $tab['dot'] }}"></span>
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Enhanced Multi-Facet Search & Filter Toolbar --}}
        <x-card class="border-slate-200/80 shadow-xs">
            <form action="{{ route('admin.finance.budgets.index') }}" method="GET" class="space-y-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Deep Search --}}
                    <div>
                        <label for="search" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Search Budgets & Activities
                        </label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Budget, project, or task activity..."
                                   class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-8 pr-7">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            @if(request('search'))
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Project Filter --}}
                    <div>
                        <label for="project_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Project</label>
                        <select name="project_id" id="project_id" class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->project_code ?? $p->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Company Filter --}}
                    <div>
                        <label for="company_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Company</label>
                        <select name="company_id" id="company_id" class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="">All Companies</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label for="sort_by" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sort By</label>
                        <select name="sort_by" id="sort_by" class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="amount_desc" {{ request('sort_by') == 'amount_desc' ? 'selected' : '' }}>Budget: Highest First</option>
                            <option value="amount_asc" {{ request('sort_by') == 'amount_asc' ? 'selected' : '' }}>Budget: Lowest First</option>
                            <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Budget Title: A to Z</option>
                        </select>
                    </div>
                </div>

                {{-- Secondary Filter Row: Min/Max Budget & Action Buttons --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                    <div>
                        <label for="min_amount" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Min Budget (RWF)</label>
                        <input type="number" step="0.01" name="min_amount" id="min_amount" value="{{ request('min_amount') }}"
                               placeholder="e.g. 10000"
                               class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    </div>

                    <div>
                        <label for="max_amount" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Max Budget (RWF)</label>
                        <input type="number" step="0.01" name="max_amount" id="max_amount" value="{{ request('max_amount') }}"
                               placeholder="e.g. 500000"
                               class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    </div>

                    @if(isset($fiscalYears) && $fiscalYears->count() > 0)
                        <div>
                            <label for="fiscal_year_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Fiscal Year</label>
                            <select name="fiscal_year_id" id="fiscal_year_id" class="w-full text-xs rounded-xl border-slate-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                <option value="">All Fiscal Years</option>
                                @foreach($fiscalYears as $fy)
                                    <option value="{{ $fy->id }}" {{ request('fiscal_year_id') == $fy->id ? 'selected' : '' }}>
                                        {{ $fy->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 text-xs font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Apply Filters
                        </button>

                        @if(request()->anyFilled(['search', 'project_id', 'company_id', 'status', 'min_amount', 'max_amount', 'fiscal_year_id', 'sort_by']))
                            <a href="{{ route('admin.finance.budgets.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </x-card>

        {{-- Budgets List Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Budget Details</th>
                            <th class="py-3.5 px-6">Project & Scope</th>
                            <th class="py-3.5 px-6">Activities Breakdown</th>
                            <th class="py-3.5 px-6 text-right">Allocated & Progress</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($budgets as $budget)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Budget Name & Date --}}
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    <div class="space-y-1">
                                        <a href="{{ route('admin.finance.budgets.show', $budget) }}" class="font-extrabold text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ $budget->name }}
                                        </a>
                                        @if($budget->description)
                                            <p class="text-[11px] text-slate-400 font-normal line-clamp-1">{{ $budget->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-2 pt-0.5 text-[10px] text-slate-400">
                                            <span>Created {{ $budget->created_at?->format('M d, Y') }}</span>
                                            @if($budget->fiscalYear)
                                                <span>•</span>
                                                <span class="inline-flex items-center font-medium text-slate-500">
                                                    FY: {{ $budget->fiscalYear->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Project & Company --}}
                                <td class="py-4 px-6">
                                    @if($budget->project)
                                        <div class="space-y-1.5">
                                            <a href="{{ route('admin.projects.show', $budget->project->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors line-clamp-1 text-xs">
                                                {{ $budget->project->name }}
                                            </a>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 text-slate-700">
                                                    {{ $budget->project->project_code ?? $budget->project->code }}
                                                </span>
                                                @if($budget->project->company)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                                                        {{ $budget->project->company->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($budget->project->manager)
                                                <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <span>PM: {{ $budget->project->manager->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">General Corporate Budget</span>
                                    @endif
                                </td>

                                {{-- Activities Count & Preview --}}
                                <td class="py-4 px-6">
                                    <div class="space-y-1.5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $budget->lines->count() }} {{ \Illuminate\Support\Str::plural('Activity', $budget->lines->count()) }}
                                        </span>
                                        @if($budget->lines->isNotEmpty())
                                            <div class="flex items-center gap-1 flex-wrap max-w-xs">
                                                @foreach($budget->lines->take(2) as $line)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 truncate max-w-[120px]">
                                                        {{ $line->activity_title }}
                                                    </span>
                                                @endforeach
                                                @if($budget->lines->count() > 2)
                                                    <span class="text-[10px] font-bold text-slate-400">
                                                        +{{ $budget->lines->count() - 2 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Total Budget Amount & Spend Progress --}}
                                <td class="py-4 px-6 text-right">
                                    <div class="space-y-1">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ format_currency($budget->total_amount, 'RWF') }}
                                        </div>

                                        @if($budget->project && $budget->project->actual_cost > 0 && $budget->total_amount > 0)
                                            @php
                                                $utilization = min(100, round(($budget->project->actual_cost / $budget->total_amount) * 100));
                                                $barColor = $utilization > 90 ? 'bg-rose-500' : ($utilization > 75 ? 'bg-amber-500' : 'bg-emerald-500');
                                            @endphp
                                            <div class="space-y-1 inline-block text-right">
                                                <div class="flex items-center justify-end gap-1.5 text-[10px]">
                                                    <span class="text-slate-400">Spent:</span>
                                                    <span class="font-semibold text-slate-700">{{ format_currency($budget->project->actual_cost, 'RWF') }}</span>
                                                    <span class="font-bold text-slate-500">({{ $utilization }}%)</span>
                                                </div>
                                                <div class="w-28 bg-slate-100 rounded-full h-1.5 overflow-hidden ml-auto">
                                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $utilization }}%"></div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-[10px] text-slate-400">
                                                0% spend recorded
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $statusType = match($budget->status) {
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'closed' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border capitalize {{ $statusType }}">
                                        {{ $budget->status }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.finance.budgets.show', $budget) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors"
                                           title="Budget vs Actual Variance Analysis">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            Vs Actuals
                                        </a>

                                        @can('update', $budget)
                                            <a href="{{ route('admin.finance.budgets.edit', $budget) }}"
                                               class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                                               title="Edit Budget">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        @endcan

                                        @can('delete', $budget)
                                            <form action="{{ route('admin.finance.budgets.destroy', $budget) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this project budget?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Delete Budget">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="max-w-xs mx-auto text-center space-y-2">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">No project budgets found</p>
                                        <p class="text-xs text-slate-400">
                                            @if(request()->anyFilled(['search', 'project_id', 'company_id', 'status', 'min_amount', 'max_amount']))
                                                Try adjusting your search terms or filters.
                                            @else
                                                Create an activity budget to plan and track project expenditures.
                                            @endif
                                        </p>
                                        <div class="pt-2 flex items-center justify-center gap-2">
                                            @if(request()->anyFilled(['search', 'project_id', 'company_id', 'status', 'min_amount', 'max_amount']))
                                                <a href="{{ route('admin.finance.budgets.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200">
                                                    Clear Filters
                                                </a>
                                            @endif
                                            @can('create', App\Models\Budget::class)
                                                <a href="{{ route('admin.finance.budgets.create') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700">
                                                    + Create First Budget
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($budgets->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $budgets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
