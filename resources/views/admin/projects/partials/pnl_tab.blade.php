<div class="space-y-6">
    {{-- Executive Profit/Loss Summary Card --}}
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Financial Health Statement</span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Project Profit & Loss (P&L) Statement</h2>
            </div>

            <div class="flex items-center gap-3">
                @if($financialSummary['is_profitable'])
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        PROFITABLE (+{{ $financialSummary['profit_margin'] }}% Margin)
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        NET LOSS ({{ $financialSummary['profit_margin'] }}% Margin)
                    </span>
                @endif
            </div>
        </div>

        {{-- Financial Summary KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60">
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Revenue</span>
                <span class="block text-2xl font-black text-slate-900 tracking-tight">{{ format_currency($financialSummary['revenue'], $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1">Invoiced: {{ format_currency($financialSummary['invoiced_revenue'], $project->currency) }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60">
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Direct Expenses</span>
                <span class="block text-2xl font-black text-amber-700 tracking-tight">{{ format_currency($financialSummary['direct_expenses'], $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1">Operational & Site costs</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60">
                <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Worker Salary & Labor</span>
                <span class="block text-2xl font-black text-indigo-700 tracking-tight">{{ format_currency($financialSummary['total_labor_cost'], $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1">Salary + Timesheets</span>
            </div>

            <div class="p-4 rounded-xl {{ $financialSummary['is_profitable'] ? 'bg-emerald-50/60 border border-emerald-200/60' : 'bg-rose-50/60 border border-rose-200/60' }}">
                <span class="block text-[11px] font-bold uppercase tracking-wider mb-1 {{ $financialSummary['is_profitable'] ? 'text-emerald-700' : 'text-rose-700' }}">Net Profit / (Loss)</span>
                <span class="block text-2xl font-black tracking-tight {{ $financialSummary['is_profitable'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ format_currency($financialSummary['net_profit'], $project->currency) }}
                </span>
                <span class="block text-[11px] font-semibold mt-1 {{ $financialSummary['is_profitable'] ? 'text-emerald-600' : 'text-rose-600' }}">
                    Margin: {{ $financialSummary['profit_margin'] }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Comprehensive P&L Income Statement Table --}}
    <x-card>
        <h3 class="text-base font-bold text-slate-900 tracking-tight mb-4">P&L Detailed Breakdown</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 font-bold text-slate-600 uppercase tracking-wider">Line Item</th>
                        <th class="py-3 px-4 font-bold text-slate-600 uppercase tracking-wider text-right">Amount ({{ $project->currency }})</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    {{-- REVENUE SECTION --}}
                    <tr class="bg-blue-50/40">
                        <td class="py-2.5 px-4 font-extrabold text-blue-900 uppercase tracking-wider">1. Project Revenue</td>
                        <td class="py-2.5 px-4"></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Invoiced Revenue (Client Invoices)</td>
                        <td class="py-2 px-4 text-right font-semibold text-slate-900">{{ format_currency($financialSummary['invoiced_revenue'], $project->currency) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Project Budget Allocation</td>
                        <td class="py-2 px-4 text-right font-semibold text-slate-900">{{ format_currency($financialSummary['budget_revenue'], $project->currency) }}</td>
                    </tr>
                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                        <td class="py-2.5 px-4 font-bold text-slate-900">Total Effective Revenue (A)</td>
                        <td class="py-2.5 px-4 text-right font-black text-slate-900 text-sm">{{ format_currency($financialSummary['revenue'], $project->currency) }}</td>
                    </tr>

                    {{-- COST & EXPENSE SECTION --}}
                    <tr class="bg-amber-50/40">
                        <td class="py-2.5 px-4 font-extrabold text-amber-900 uppercase tracking-wider">2. Project Expenses & Costs</td>
                        <td class="py-2.5 px-4"></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Direct Worker Salary & Wage Payouts</td>
                        <td class="py-2 px-4 text-right font-semibold text-indigo-700">{{ format_currency($financialSummary['direct_labor_expenses'], $project->currency) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Timesheet Work Hours Cost</td>
                        <td class="py-2 px-4 text-right font-semibold text-indigo-700">{{ format_currency($financialSummary['time_entries_labor_cost'], $project->currency) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Direct Operational Expenses (Equipment, Logistics, Site)</td>
                        <td class="py-2 px-4 text-right font-semibold text-amber-700">{{ format_currency($financialSummary['direct_expenses'], $project->currency) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 pl-8 text-slate-700">Issued Materials & Supplies Cost</td>
                        <td class="py-2 px-4 text-right font-semibold text-blue-700">{{ format_currency($financialSummary['material_cost'], $project->currency) }}</td>
                    </tr>
                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                        <td class="py-2.5 px-4 font-bold text-slate-900">Total Costs & Expenses (B)</td>
                        <td class="py-2.5 px-4 text-right font-black text-rose-700 text-sm">{{ format_currency($financialSummary['total_costs'], $project->currency) }}</td>
                    </tr>

                    {{-- NET PROFIT SECTION --}}
                    <tr class="{{ $financialSummary['is_profitable'] ? 'bg-emerald-100/50' : 'bg-rose-100/50' }} border-t-2 border-slate-300">
                        <td class="py-3.5 px-4 font-black text-sm uppercase tracking-wider {{ $financialSummary['is_profitable'] ? 'text-emerald-900' : 'text-rose-900' }}">
                            Net Project Profit / (Loss) (A - B)
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-base {{ $financialSummary['is_profitable'] ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ format_currency($financialSummary['net_profit'], $project->currency) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</div>
