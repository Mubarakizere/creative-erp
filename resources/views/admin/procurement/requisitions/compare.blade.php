<x-layouts.admin title="Compare Quotations - {{ $requisition->code }}">
    <div class="space-y-6" x-data="{ viewMode: 'cards', selectedQuoteId: null, selectedSupplierName: '', acceptFormUrl: '' }">
        
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.procurement.requisitions.index') }}" class="hover:text-indigo-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Purchase Requisitions
                    </a>
                    <span>/</span>
                    <a href="{{ route('admin.procurement.requisitions.show', $requisition->id) }}" class="hover:text-indigo-600 font-medium transition-colors">
                        {{ $requisition->code }}
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">Compare Quotations</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Quotation Comparison
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $requisition->code }}
                    </span>
                </div>
            </div>

            {{-- Actions & View Switcher --}}
            <div class="flex flex-wrap items-center gap-3">
                @if($requisition->quotations->count() > 1)
                    <div class="bg-slate-100 p-1 rounded-xl flex items-center border border-slate-200">
                        <button @click="viewMode = 'cards'" 
                                :class="viewMode === 'cards' ? 'bg-white text-indigo-600 shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Cards View
                        </button>
                        <button @click="viewMode = 'matrix'" 
                                :class="viewMode === 'matrix' ? 'bg-white text-indigo-600 shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Comparison Matrix
                        </button>
                    </div>
                @endif

                <a href="{{ route('admin.procurement.rfqs.create', ['purchase_requisition_id' => $requisition->id]) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-colors shadow-xs">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Record Supplier Quote
                </a>

                <a href="{{ route('admin.procurement.requisitions.show', $requisition->id) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    View Requisition
                </a>
            </div>
        </div>

        {{-- Requisition Metadata Summary Banner --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                <div class="flex items-center gap-3.5 pr-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Requisition Code</div>
                        <div class="text-sm font-bold text-slate-900">{{ $requisition->code }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $requisition->created_at ? $requisition->created_at->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Project / Dept</div>
                        <div class="text-sm font-bold text-slate-900 truncate max-w-[170px]" title="{{ $requisition->project?->name ?? $requisition->department?->name ?? 'General Procurement' }}">
                            {{ $requisition->project?->name ?? $requisition->department?->name ?? 'General Procurement' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Requested by {{ $requisition->requestedBy?->name ?? 'System' }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Requested Line Items</div>
                        <div class="text-sm font-bold text-slate-900">{{ $requisition->items->count() }} Items</div>
                        <div class="text-xs text-slate-500 mt-0.5">Total items in requisition</div>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 pt-3 sm:pt-0 sm:pl-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Quotations Received</div>
                        <div class="text-sm font-bold text-slate-900">{{ $requisition->quotations->count() }} Supplier Quotes</div>
                        <div class="text-xs text-slate-500 mt-0.5">Ready for review & decision</div>
                    </div>
                </div>
            </div>
        </div>

        @if($requisition->quotations->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">No Quotations Received Yet</h3>
                <p class="text-slate-500 text-sm max-w-md mx-auto mb-6">
                    There are currently no supplier quotations submitted for purchase requisition <span class="font-semibold text-slate-700">{{ $requisition->code }}</span>.
                </p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('admin.procurement.requisitions.show', $requisition->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View Requisition Details
                    </a>
                </div>
            </div>
        @else
            @php
                // Compute quotation totals and find lowest quotation
                $quotationTotals = [];
                foreach($requisition->quotations as $quote) {
                    $qTotal = $quote->items->sum(function($item) {
                        return $item->total > 0 
                            ? $item->total 
                            : (($item->quantity * $item->unit_price) - $item->discount + $item->tax);
                    });
                    $quotationTotals[$quote->id] = $qTotal;
                }
                $minQuoteId = !empty($quotationTotals) ? array_keys($quotationTotals, min($quotationTotals))[0] : null;
                $minQuoteTotal = $minQuoteId ? $quotationTotals[$minQuoteId] : 0;
                $avgQuoteTotal = !empty($quotationTotals) ? array_sum($quotationTotals) / count($quotationTotals) : 0;
            @endphp

            {{-- Best Deal Highlights Banner --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 rounded-2xl p-5 text-white shadow-md relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100 bg-white/15 px-2.5 py-0.5 rounded-full backdrop-blur-xs">
                            Lowest Total Quote
                        </span>
                        <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @php
                        $bestQuote = $requisition->quotations->firstWhere('id', $minQuoteId);
                    @endphp
                    <div class="text-2xl font-black tracking-tight">
                        {{ format_currency($minQuoteTotal, session('currency')) }}
                    </div>
                    <div class="text-xs text-emerald-100 mt-1 flex items-center gap-1">
                        <span>Offered by</span>
                        <span class="font-bold text-white underline decoration-emerald-300 underline-offset-2">{{ $bestQuote->supplier?->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                        <span>Average Quote Value</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 my-1">
                        {{ format_currency($avgQuoteTotal, session('currency')) }}
                    </div>
                    <div class="text-xs text-slate-500">
                        Based on {{ $requisition->quotations->count() }} participating suppliers
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                        <span>Potential Savings</span>
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    @php
                        $maxQuoteTotal = max($quotationTotals);
                        $savings = $maxQuoteTotal - $minQuoteTotal;
                    @endphp
                    <div class="text-2xl font-extrabold text-emerald-600 my-1">
                        {{ format_currency($savings, session('currency')) }}
                    </div>
                    <div class="text-xs text-slate-500">
                        Max variance between lowest and highest quote
                    </div>
                </div>
            </div>

            {{-- VIEW 1: Side-by-Side Quotations Cards --}}
            <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($requisition->quotations as $quotation)
                    @php
                        $isLowest = ($quotation->id === $minQuoteId);
                        $quoteTotal = $quotationTotals[$quotation->id] ?? 0;
                        $statusNormalized = strtolower($quotation->status ?? 'draft');
                        $statusClasses = [
                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'submitted' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ][$statusNormalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp

                    <div class="bg-white rounded-2xl border transition-all duration-200 flex flex-col justify-between shadow-xs hover:shadow-md relative overflow-hidden {{ $isLowest ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80' }}">
                        @if($isLowest)
                            <div class="bg-emerald-500 text-white text-[11px] font-extrabold uppercase tracking-wider py-1 px-4 text-center flex items-center justify-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Recommended • Lowest Price
                            </div>
                        @endif

                        <div class="p-5 flex-1">
                            {{-- Supplier Info Header --}}
                            <div class="flex items-start justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $quotation->supplier?->name ?? 'Unknown Supplier' }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                        <span class="font-medium text-slate-600">Quote: {{ $quotation->code }}</span>
                                        @if($quotation->lead_time_days)
                                            <span>•</span>
                                            <span>{{ $quotation->lead_time_days }} days lead time</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize shrink-0 {{ $statusClasses }}">
                                    {{ $quotation->status }}
                                </span>
                            </div>

                            {{-- Itemized Breakdown Table --}}
                            <div class="space-y-3 mb-4">
                                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Itemized Quote Details</div>
                                @foreach($quotation->items as $item)
                                    @php
                                        $itemTotal = $item->total > 0 
                                            ? $item->total 
                                            : (($item->quantity * $item->unit_price) - $item->discount + $item->tax);
                                    @endphp
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                        <div class="flex items-center justify-between font-bold text-slate-800 mb-1">
                                            <span class="truncate max-w-[180px]">{{ $item->product?->name ?? 'Product Item' }}</span>
                                            <span class="text-slate-900 font-extrabold">{{ format_currency($itemTotal, session('currency')) }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-between text-slate-500 text-[11px] gap-1">
                                            <span>Qty: {{ number_format($item->quantity, 2) }} {{ $item->product?->unit?->code ?? '' }}</span>
                                            <span>Unit: {{ format_currency($item->unit_price, session('currency')) }}</span>
                                            @if($item->discount > 0)
                                                <span class="text-emerald-600 font-semibold">Disc: -{{ format_currency($item->discount, session('currency')) }}</span>
                                            @endif
                                            @if($item->tax > 0)
                                                <span>Tax: +{{ format_currency($item->tax, session('currency')) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($quotation->notes || $quotation->terms)
                                <div class="text-xs text-slate-500 bg-slate-50/70 p-3 rounded-xl border border-slate-100 space-y-1">
                                    @if($quotation->terms)
                                        <div><span class="font-semibold text-slate-700">Terms:</span> {{ Str::limit($quotation->terms, 80) }}</div>
                                    @endif
                                    @if($quotation->notes)
                                        <div><span class="font-semibold text-slate-700">Notes:</span> {{ Str::limit($quotation->notes, 80) }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Card Footer & Actions --}}
                        <div class="p-5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-4">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Quotation</div>
                                <div class="text-xl font-extrabold {{ $isLowest ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ format_currency($quoteTotal, session('currency')) }}
                                </div>
                            </div>

                            @if($quotation->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Accepted & PO Issued
                                </span>
                            @else
                                <button type="button" 
                                        @click="selectedQuoteId = {{ $quotation->id }}; selectedSupplierName = '{{ addslashes($quotation->supplier?->name ?? 'Supplier') }}'; acceptFormUrl = '{{ route('admin.procurement.requisitions.accept', [$requisition->id, $quotation->id]) }}'; $dispatch('open-modal', 'accept-quotation-modal')"
                                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-xs {{ $isLowest ? 'bg-emerald-600 hover:bg-emerald-700 ring-2 ring-emerald-600/30' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Accept & PO
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- VIEW 2: Comparison Matrix Table --}}
            <div x-show="viewMode === 'matrix'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Side-by-Side Comparison Matrix</h3>
                        <p class="text-xs text-slate-500">Compare product unit prices across all participating vendors</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Green highlight indicates lowest unit price
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                                <th class="py-3.5 px-4 min-w-[220px]">Requested Product / Item</th>
                                <th class="py-3.5 px-4 text-center min-w-[100px]">Qty</th>
                                @foreach($requisition->quotations as $quote)
                                    <th class="py-3.5 px-4 text-right min-w-[160px] border-l border-slate-200 {{ $quote->id === $minQuoteId ? 'bg-emerald-50/60 text-emerald-900' : '' }}">
                                        <div class="font-extrabold text-sm">{{ $quote->supplier?->name ?? 'Supplier' }}</div>
                                        <div class="text-[11px] font-normal text-slate-500 uppercase">Quote: {{ $quote->code }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @foreach($requisition->items as $reqItem)
                                @php
                                    // Find lowest unit price for this item across all quotes
                                    $unitPrices = [];
                                    foreach($requisition->quotations as $q) {
                                        $qItem = $q->items->firstWhere('product_id', $reqItem->product_id);
                                        if($qItem) {
                                            $unitPrices[$q->id] = $qItem->unit_price;
                                        }
                                    }
                                    $minUnitPrice = !empty($unitPrices) ? min($unitPrices) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        {{ $reqItem->product?->name ?? 'Product' }}
                                        @if($reqItem->product?->unit)
                                            <span class="text-slate-400 font-normal text-[11px]">({{ $reqItem->product->unit->name }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-600">
                                        {{ number_format($reqItem->quantity, 2) }}
                                    </td>
                                    @foreach($requisition->quotations as $quote)
                                        @php
                                            $qItem = $quote->items->firstWhere('product_id', $reqItem->product_id);
                                            $isLowestUnit = ($qItem && $qItem->unit_price == $minUnitPrice && count($unitPrices) > 1);
                                        @endphp
                                        <td class="py-3.5 px-4 text-right border-l border-slate-200 {{ $quote->id === $minQuoteId ? 'bg-emerald-50/20' : '' }}">
                                            @if($qItem)
                                                <div class="font-extrabold {{ $isLowestUnit ? 'text-emerald-600 font-black' : 'text-slate-900' }}">
                                                    {{ format_currency($qItem->unit_price, session('currency')) }}
                                                    @if($isLowestUnit)
                                                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 ml-1" title="Lowest Unit Price"></span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-400">
                                                    Total: {{ format_currency($qItem->total > 0 ? $qItem->total : ($qItem->quantity * $qItem->unit_price), session('currency')) }}
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">Not Quoted</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100/90 border-t-2 border-slate-300 font-extrabold text-slate-900">
                                <td class="py-4 px-4 text-sm" colspan="2">
                                    GRAND TOTAL
                                </td>
                                @foreach($requisition->quotations as $quote)
                                    @php
                                        $qTotal = $quotationTotals[$quote->id] ?? 0;
                                        $isLowest = ($quote->id === $minQuoteId);
                                    @endphp
                                    <td class="py-4 px-4 text-right border-l border-slate-300 {{ $isLowest ? 'bg-emerald-100/80 text-emerald-950' : '' }}">
                                        <div class="text-base font-black {{ $isLowest ? 'text-emerald-700' : 'text-slate-900' }}">
                                            {{ format_currency($qTotal, session('currency')) }}
                                        </div>
                                        @if($isLowest)
                                            <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-800 bg-emerald-200/80 px-2 py-0.5 rounded-full inline-block mt-0.5">
                                                Lowest Total
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="bg-slate-50 border-t border-slate-200">
                                <td class="py-3 px-4" colspan="2">Action</td>
                                @foreach($requisition->quotations as $quote)
                                    <td class="py-3 px-4 text-right border-l border-slate-200">
                                        @if($quote->status === 'approved')
                                            <span class="text-xs font-bold text-emerald-700">Accepted</span>
                                        @else
                                            <button type="button" 
                                                    @click="selectedQuoteId = {{ $quote->id }}; selectedSupplierName = '{{ addslashes($quote->supplier?->name ?? 'Supplier') }}'; acceptFormUrl = '{{ route('admin.procurement.requisitions.accept', [$requisition->id, $quote->id]) }}'; $dispatch('open-modal', 'accept-quotation-modal')"
                                                    class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-xs">
                                                Accept Quote
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Confirmation Modal for Accepting Quotation using Workspace Global <x-modal> Component --}}
        <x-modal id="accept-quotation-modal" maxWidth="2xl">
            <x-slot:header>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Accept Supplier Quotation</h3>
                        <p class="text-xs text-slate-500 font-normal">Confirm quotation selection and generate Purchase Order (PO)</p>
                    </div>
                </div>
            </x-slot:header>

            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Selected Supplier</div>
                        <div class="text-lg font-black text-slate-900" x-text="selectedSupplierName"></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Requisition</div>
                        <div class="text-sm font-bold text-indigo-600">{{ $requisition->code }}</div>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <span class="font-bold">Next Action Notice:</span> Accepting this quotation will mark the selected supplier quote as approved, update requisition status, and automatically create a new draft Purchase Order (PO) pre-populated with all agreed quantities and prices.
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <button type="button" @click="$dispatch('close-modal', 'accept-quotation-modal')" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 rounded-xl transition-colors shadow-xs">
                    Cancel
                </button>
                <form :action="acceptFormUrl" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="px-6 py-2 text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm & Generate Purchase Order
                    </button>
                </form>
            </x-slot:footer>
        </x-modal>

    </div>
</x-layouts.admin>