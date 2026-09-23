<x-layouts.admin title="Project Budget - {{ $budget->name }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Project Budgets', 'url' => route('admin.finance.budgets.index')],
                ['label' => $budget->name],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        {{-- Executive Hero Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.finance.budgets.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Project Budgets
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $budget->name }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $budget->name }}
                    </h1>
                    @php
                        $statusType = match($budget->status) {
                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'closed' => 'bg-slate-100 text-slate-600 border-slate-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-xs capitalize {{ $statusType }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                        {{ $budget->status }}
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                @if($budget->project)
                    <a href="{{ route('admin.projects.show', $budget->project->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        View Project Profile
                    </a>
                @endif

                @can('update', $budget)
                    <a href="{{ route('admin.finance.budgets.edit', $budget) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Budget
                    </a>
                @endcan

                <a href="{{ route('admin.finance.budgets.index') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Project Context Card --}}
        @if($budget->project)
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-sm border border-slate-800">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-white/10 text-indigo-300 border border-white/10">
                                {{ $budget->project->project_code ?? $budget->project->code }}
                            </span>
                            @if($budget->project->company)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-200 border border-indigo-500/30">
                                    {{ $budget->project->company->name }}
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl font-black tracking-tight text-white">
                            <a href="{{ route('admin.projects.show', $budget->project->id) }}" class="hover:underline">
                                {{ $budget->project->name }}
                            </a>
                        </h2>
                        <p class="text-xs text-slate-300 max-w-2xl line-clamp-1">
                            {{ $budget->description ?: ($budget->project->description ?: 'No additional project description recorded.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs shrink-0">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <span class="block text-slate-400 font-medium">Project Manager</span>
                            <span class="font-bold text-white mt-0.5 block truncate">
                                {{ $budget->project->manager?->full_name ?? $budget->project->manager?->name ?? 'Not Assigned' }}
                            </span>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <span class="block text-slate-400 font-medium">Client</span>
                            <span class="font-bold text-white mt-0.5 block truncate">
                                {{ $budget->project->client?->display_name ?? 'Internal / General' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 4 Executive Summary KPI Cards --}}
        @php
            $summary = $analysis['summary'];
            $totalBudget = $summary['budget'];
            $totalActual = $summary['actual'];
            $variance = $summary['variance'];
            $utilization = $totalBudget > 0 ? round(($totalActual / $totalBudget) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Allocated Budget --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Allocated Budget</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black text-slate-900 tracking-tight">{{ format_currency($totalBudget, session('currency')) }}</span>
                    <p class="text-xs text-slate-500 mt-1">Across {{ count($analysis['lines']) }} activity lines</p>
                </div>
            </div>

            {{-- Card 2: Actual Cost Spent --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Actual Cost Incurred</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black text-slate-900 tracking-tight">{{ format_currency($totalActual, session('currency')) }}</span>
                    <p class="text-xs text-slate-500 mt-1">Materials issued & direct costs</p>
                </div>
            </div>

            {{-- Card 3: Variance / Remaining Balance --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Remaining Balance</span>
                    <div class="w-8 h-8 rounded-xl {{ $variance >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight {{ $variance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $variance >= 0 ? '+' : '' }}{{ format_currency($variance, session('currency')) }}
                    </span>
                    <p class="text-xs {{ $variance >= 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-1 font-semibold">
                        {{ $variance >= 0 ? 'Under budget (Safe)' : 'Budget overrun!' }}
                    </p>
                </div>
            </div>

            {{-- Card 4: Utilization Rate --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Budget Utilization</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $utilization > 100 ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($utilization > 90 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                        {{ $summary['status'] }}
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline justify-between mb-1.5">
                        <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $utilization }}%</span>
                        <span class="text-xs text-slate-400">100% cap</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-700 {{ $utilization > 100 ? 'bg-rose-600' : ($utilization > 90 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                             style="width: {{ min(100, $utilization) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Breakdown Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Project Activities Budget Breakdown</h3>
                    <p class="text-xs text-slate-500">Itemized fund allocations and real-time expenditure per project activity.</p>
                </div>
                <span class="text-xs font-bold text-slate-500">
                    {{ count($analysis['lines']) }} Activities Tracked
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Project Activity / Task</th>
                            <th class="py-3.5 px-6">Cost Category</th>
                            <th class="py-3.5 px-6 text-right">Allocated Budget</th>
                            <th class="py-3.5 px-6 text-right">Actual Spent</th>
                            <th class="py-3.5 px-6 text-right">Variance ($)</th>
                            <th class="py-3.5 px-6 text-right">Utilization</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($analysis['lines'] as $line)
                            @php
                                $lineRatio = $line['budget_amount'] > 0 ? round(($line['actual_amount'] / $line['budget_amount']) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Activity / Task --}}
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            @if($line['task_code'])
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $line['task_code'] }}
                                                </span>
                                            @endif
                                            <span>{{ $line['task_name'] }}</span>
                                        </div>
                                        @if(!empty($line['notes']))
                                            <p class="text-[11px] text-slate-400 font-normal">{{ $line['notes'] }}</p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Cost Category --}}
                                <td class="py-3.5 px-6 text-slate-600 font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                        {{ $line['category'] }}
                                    </span>
                                </td>

                                {{-- Budget Amount --}}
                                <td class="py-3.5 px-6 text-right font-extrabold text-slate-900">
                                    {{ format_currency($line['budget_amount'], session('currency')) }}
                                </td>

                                {{-- Actual Amount --}}
                                <td class="py-3.5 px-6 text-right font-bold text-slate-700">
                                    {{ format_currency($line['actual_amount'], session('currency')) }}
                                </td>

                                {{-- Variance ($) --}}
                                <td class="py-3.5 px-6 text-right font-bold {{ $line['variance'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $line['variance'] >= 0 ? '+' : '' }}{{ format_currency($line['variance'], session('currency')) }}
                                </td>

                                {{-- Utilization (%) --}}
                                <td class="py-3.5 px-6 text-right font-semibold text-slate-700">
                                    {{ $lineRatio }}%
                                </td>

                                {{-- Status Badge --}}
                                <td class="py-3.5 px-6 text-center">
                                    @php
                                        $badgeClass = match($line['status']) {
                                            'exceeded' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'on_track' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border capitalize {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', $line['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">
                                    No activity allocations found in this budget.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100/90 border-t-2 border-slate-300 font-extrabold text-slate-900">
                            <td class="py-4 px-6 text-xs uppercase tracking-wider" colspan="2">
                                TOTAL PROJECT BUDGET
                            </td>
                            <td class="py-4 px-6 text-right text-sm font-black text-slate-900">
                                {{ format_currency($totalBudget, session('currency')) }}
                            </td>
                            <td class="py-4 px-6 text-right text-sm font-black text-slate-700">
                                {{ format_currency($totalActual, session('currency')) }}
                            </td>
                            <td class="py-4 px-6 text-right text-sm font-black {{ $variance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $variance >= 0 ? '+' : '' }}{{ format_currency($variance, session('currency')) }}
                            </td>
                            <td class="py-4 px-6 text-right text-xs font-bold text-slate-700">
                                {{ $utilization }}%
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
