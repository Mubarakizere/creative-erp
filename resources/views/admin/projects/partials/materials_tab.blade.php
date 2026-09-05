@php
    // Calculate Material Costs & P&L for this project
    $allIssues = $project->materialIssues()->with(['warehouse', 'issuer', 'items.product.defaultSupplier', 'items.product.unit'])->latest()->get();
    
    $totalMaterialCost = 0;
    $totalIssuesCount = $allIssues->count();
    
    // Group consumption by product
    $materialBreakdown = [];
    foreach ($allIssues as $issue) {
        foreach ($issue->items as $item) {
            $totalMaterialCost += $item->total_cost ?? 0;
            $productId = $item->product_id;
            
            if (!isset($materialBreakdown[$productId])) {
                $materialBreakdown[$productId] = [
                    'product' => $item->product,
                    'quantity' => 0,
                    'total_cost' => 0,
                ];
            }
            $materialBreakdown[$productId]['quantity'] += $item->quantity;
            $materialBreakdown[$productId]['total_cost'] += ($item->total_cost ?? 0);
        }
    }
    
    // Calculate P&L metrics
    $contractRevenue = $project->actual_budget > 0 ? $project->actual_budget : ($project->estimated_budget ?? 0);
    $totalProjectCost = max($project->actual_cost ?? 0, $totalMaterialCost);
    $netProfit = $contractRevenue - $totalProjectCost;
    $profitMargin = $contractRevenue > 0 ? round(($netProfit / $contractRevenue) * 100, 1) : 0;
@endphp

<div class="space-y-6">
    {{-- Top Action Bar & Financial Summary Cards --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Material Costs & Project P&L</h3>
            <p class="text-xs text-slate-500 mt-0.5">Real-time valuation of stock issued to project vs project revenue</p>
        </div>
        @if($project->hasPermissionForUser(auth()->user(), 'material_request.create') || $project->hasPermissionForUser(auth()->user(), 'material_issue.create'))
            <x-button type="primary" href="{{ route('admin.project-material-issues.create') }}?project_id={{ $project->id }}" size="sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Issue Material to Site
            </x-button>
        @endif
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Material Expense --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Material Cost Consumed</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900">{{ format_currency($totalMaterialCost, $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1 font-medium">{{ $totalIssuesCount }} site issue records</span>
            </div>
        </div>

        {{-- Contract Value --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Project Value / Budget</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900">{{ format_currency($contractRevenue, $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1 font-medium">Invoiced / Contract Value</span>
            </div>
        </div>

        {{-- Total Project Cost --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Actual Costs</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900">{{ format_currency($totalProjectCost, $project->currency) }}</span>
                <span class="block text-[11px] text-slate-500 mt-1 font-medium">Materials + Labor & Overhead</span>
            </div>
        </div>

        {{-- Net Project Profit / Loss --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Net Profit / Margin</span>
                <div class="w-8 h-8 rounded-lg {{ $netProfit >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ format_currency($netProfit, $project->currency) }}
                </span>
                <div class="mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $profitMargin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $profitMargin }}% Profit Margin
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Material Breakdown Table (Grouped by Item) --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-slate-900 tracking-tight">Material Consumption Breakdown</h4>
            <span class="text-xs text-slate-500 font-medium">Valued based on Purchase Cost & Inventory Stock Issue</span>
        </div>

        <x-table>
            <x-slot:head>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Material / Item</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Default Supplier</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Qty Issued</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Avg Cost / Unit</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Material Cost</th>
            </x-slot:head>

            @forelse($materialBreakdown as $productId => $data)
                @php
                    $prod = $data['product'];
                    $qty = $data['quantity'];
                    $totalCost = $data['total_cost'];
                    $avgCost = $qty > 0 ? $totalCost / $qty : 0;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-slate-900 text-sm">{{ $prod?->name ?? 'Unknown Item' }}</div>
                        <div class="text-xs text-slate-500">SKU: {{ $prod?->sku ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-slate-700">{{ $prod?->defaultSupplier?->name ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-bold text-slate-900 text-sm">{{ number_format($qty, 2) }}</span>
                        <span class="text-xs text-slate-500 ml-1">{{ $prod?->unit?->code ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-medium text-slate-700">
                        {{ format_currency($avgCost, $project->currency) }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-slate-900">
                        {{ format_currency($totalCost, $project->currency) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                        No materials issued to this project yet.
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    {{-- Material Issue Slips Audit Log --}}
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-bold text-slate-900 tracking-tight">Site Material Issue Slips</h4>
            <span class="text-xs text-slate-500 font-medium">Detailed Goods Issue Transactions</span>
        </div>

        <x-table>
            <x-slot:head>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Issue Slip #</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Source Warehouse</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Issued By</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Value</th>
            </x-slot:head>

            @forelse($allIssues as $issue)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.project-material-issues.show', $issue) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                            {{ $issue->issue_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">
                        {{ $issue->issue_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700">
                        {{ $issue->warehouse->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700">
                        {{ $issue->issuer->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-slate-900">
                        {{ format_currency($issue->items->sum('total_cost'), $project->currency) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">No material issue slips recorded.</td>
                </tr>
            @endforelse
        </x-table>
    </x-card>
</div>
