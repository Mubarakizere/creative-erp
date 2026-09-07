<x-layouts.admin title="Purchase Requisition {{ $requisition->code }}">
    <div class="space-y-6">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.requisitions.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Purchase Requisitions
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $requisition->code }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Purchase Requisition {{ $requisition->code }}
                    </h1>
                    @php
                        $statusNormalized = strtolower($requisition->status ?? 'draft');
                        $statusClasses = [
                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'under_review' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                        ][$statusNormalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-xs capitalize {{ $statusClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                        {{ $requisition->status }}
                    </span>
                </div>
            </div>

            {{-- Actions Bar --}}
            <div class="flex flex-wrap items-center gap-2">
                @if(in_array(strtolower($requisition->status), ['submitted', 'draft']) && auth()->user()->can('approve', $requisition))
                    <form action="{{ route('admin.procurement.requisitions.approve', $requisition) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve Requisition
                        </button>
                    </form>
                @endif

                @if(strtolower($requisition->status) === 'approved')
                    <a href="{{ route('admin.procurement.rfqs.create', ['purchase_requisition_id' => $requisition->id]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Record Supplier Quote
                    </a>
                @endif

                <a href="{{ route('admin.procurement.requisitions.compare', $requisition->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Compare Quotations
                </a>

                <a href="{{ route('admin.procurement.requisitions.index') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Linked Material Request Banner (if converted from site request) --}}
        @if($requisition->projectMaterialRequest)
            <div class="bg-blue-50/80 rounded-2xl border border-blue-200 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-950">Converted from Material Request</h4>
                        <p class="text-xs text-blue-700 mt-0.5">
                            Request Ref: <strong>{{ $requisition->projectMaterialRequest->request_number }}</strong> — Site Request Status: <span class="font-semibold">{{ $requisition->projectMaterialRequest->status }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.material-requests.show', $requisition->projectMaterialRequest) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-white border border-blue-200 hover:bg-blue-50 shadow-xs transition-colors self-start sm:self-auto">
                    View Material Request &rarr;
                </a>
            </div>
        @endif

        {{-- Main Requisition Details Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Requisition Overview
                </h3>
                <span class="text-xs font-semibold text-slate-400">PR Ref: #{{ $requisition->id }}</span>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Project / Department --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Project / Scope</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold text-xs border border-indigo-100">
                            PR
                        </div>
                        <div>
                            @if($requisition->project)
                                <a href="{{ route('admin.projects.show', $requisition->project_id) }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition-colors block leading-snug">
                                    {{ $requisition->project->name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $requisition->project->project_code ?? '' }}</span>
                            @else
                                <p class="text-sm font-bold text-slate-900 leading-snug">{{ $requisition->department?->name ?? 'General Procurement' }}</p>
                                <span class="text-xs text-slate-500">Internal Department</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Requested By --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Requested By</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-xs border border-slate-200">
                            {{ strtoupper(substr($requisition->requestedBy->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-snug">{{ $requisition->requestedBy->name ?? 'System Staff' }}</p>
                            <p class="text-xs text-slate-500">{{ $requisition->requestedBy->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Requisition Dates</span>
                    <div>
                        <p class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Created: {{ $requisition->created_at ? $requisition->created_at->format('M d, Y') : 'N/A' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Required by: <strong class="text-slate-700">{{ $requisition->required_date ? $requisition->required_date->format('M d, Y') : 'As Soon As Possible' }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Priority --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Priority Level</span>
                    <div>
                        @php
                            $priorityNormalized = strtolower($requisition->priority ?? 'normal');
                            $priorityColors = [
                                'urgent' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'high' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'normal' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'low' => 'bg-slate-100 text-slate-700 border-slate-200',
                            ][$priorityNormalized] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-extrabold border uppercase {{ $priorityColors }}">
                            {{ $requisition->priority ?? 'Normal' }} Priority
                        </span>
                    </div>
                </div>

                {{-- Notes --}}
                @if($requisition->notes)
                    <div class="col-span-1 md:col-span-2 lg:col-span-4 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Requisition Notes & Justification</span>
                        <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100 font-medium">
                            {{ $requisition->notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Requested Items Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Requisition Items</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Products and quantities to acquire</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                    {{ $requisition->items->count() }} {{ Str::plural('item', $requisition->items->count()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Product / Item</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Quantity Needed</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Item Description / Specification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($requisition->items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-3 text-sm">
                                    <div class="font-bold text-slate-900">{{ $item->product->name ?? 'Unknown Product' }}</div>
                                    <div class="text-xs text-slate-500 font-mono">SKU: {{ $item->product->sku ?? '—' }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm text-right font-extrabold text-slate-900">
                                    <span class="text-base">{{ number_format($item->quantity, 2) }}</span>
                                    <span class="text-xs font-medium text-slate-500 ml-1">{{ $item->product->unit->name ?? ($item->product->unit->code ?? 'Unit') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $item->description ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-500">
                                    No items listed in this requisition.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Supplier Quotations Summary (if any exist) --}}
        @if($requisition->quotations && $requisition->quotations->count() > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Received Supplier Quotations</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Quotations submitted for this requisition</p>
                    </div>
                    <a href="{{ route('admin.procurement.requisitions.compare', $requisition->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Compare All &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Supplier</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Total Price</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Quotation Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($requisition->quotations as $quote)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 pl-6 pr-3 text-sm font-bold text-slate-900">
                                        {{ $quote->supplier->name ?? 'Supplier' }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-right font-extrabold text-slate-900">
                                        {{ format_currency($quote->total ?? 0, session('currency')) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-slate-100 text-slate-700">
                                            {{ $quote->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>