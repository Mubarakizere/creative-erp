<x-layouts.admin title="Material Request {{ $materialRequest->request_number }}">
    <div class="space-y-6">
        {{-- Navigation & Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1.5">
                    <a href="{{ route('admin.material-requests.index') }}" class="hover:text-blue-600 font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Material Requests
                    </a>
                    <span>/</span>
                    <span class="font-semibold text-slate-700">{{ $materialRequest->request_number }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Material Request {{ $materialRequest->request_number }}
                    </h1>
                    @php
                        $statusClasses = [
                            'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'Submitted' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'Under Review' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Issued' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'Cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                        ][$materialRequest->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-xs {{ $statusClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                        {{ $materialRequest->status }}
                    </span>
                </div>
            </div>

            {{-- Actions Bar --}}
            <div class="flex flex-wrap items-center gap-2">
                @if($materialRequest->status === 'Draft' && auth()->user()->can('submit', $materialRequest))
                    <form action="{{ route('admin.material-requests.submit', $materialRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Submit for Approval
                        </button>
                    </form>
                @endif

                @if(in_array($materialRequest->status, ['Submitted', 'Under Review']) && auth()->user()->can('approve', $materialRequest))
                    <form action="{{ route('admin.material-requests.approve', $materialRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve
                        </button>
                    </form>
                @endif

                @if($materialRequest->status === 'Approved' && auth()->user()->can('create', App\Models\ProjectMaterialIssue::class))
                    <a href="{{ route('admin.project-material-issues.create', ['project_id' => $materialRequest->project_id, 'material_request_id' => $materialRequest->id]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Issue Material
                    </a>
                @endif

                @if(in_array($materialRequest->status, ['Submitted', 'Under Review']) && auth()->user()->can('reject', $materialRequest))
                    <form action="{{ route('admin.material-requests.reject', $materialRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </button>
                    </form>
                @endif

                @if(!$materialRequest->purchaseRequisition && auth()->user()->can('convertToProcurement', $materialRequest))
                    <form action="{{ route('admin.material-requests.convert', $materialRequest) }}" method="POST" id="convert-pr-form">
                        @csrf
                        <button type="button" x-data @click="$dispatch('open-convert-pr-modal')" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Convert to PR
                        </button>
                    </form>
                @endif

                @if($materialRequest->status !== 'Approved' && $materialRequest->status !== 'Cancelled' && auth()->user()->can('cancel', $materialRequest))
                    <form action="{{ route('admin.material-requests.cancel', $materialRequest) }}" method="POST" id="cancel-request-form">
                        @csrf
                        <button type="button" x-data @click="$dispatch('open-cancel-request-modal')" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                            Cancel
                        </button>
                    </form>
                @endif

                @can('update', $materialRequest)
                    <a href="{{ route('admin.material-requests.edit', $materialRequest) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                @endcan
            </div>
        </div>

        {{-- Main Metadata Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Request Overview
                </h3>
                <span class="text-xs font-semibold text-slate-400">Request Ref: #{{ $materialRequest->id }}</span>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                {{-- Company --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Company</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-extrabold text-xs border border-purple-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-snug">
                                {{ $materialRequest->company->name ?? 'Default Company' }}
                            </p>
                            <span class="text-xs text-slate-500 font-medium">{{ $materialRequest->company->email ?? '' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Project --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Project</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-xs border border-blue-100">
                            PR
                        </div>
                        <div>
                            <a href="{{ route('admin.projects.show', $materialRequest->project_id) }}" class="text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors block leading-snug">
                                {{ $materialRequest->project->name }}
                            </a>
                            <span class="text-xs text-slate-500 font-mono">{{ $materialRequest->project->project_code ?? '' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Requested By --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Requested By</span>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-xs border border-slate-200">
                            {{ strtoupper(substr($materialRequest->requestedBy->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-snug">{{ $materialRequest->requestedBy->name ?? 'Unknown User' }}</p>
                            <p class="text-xs text-slate-500">{{ $materialRequest->requestedBy->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Request Date & Required Date --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dates Timeline</span>
                    <div>
                        <p class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Requested: {{ $materialRequest->request_date->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Required by: <strong class="text-slate-700">{{ $materialRequest->required_date ? $materialRequest->required_date->format('M d, Y') : 'N/A' }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Priority --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Priority Level</span>
                    <div>
                        @php
                            $priorityColors = [
                                'Urgent' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'High' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'Normal' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Low' => 'bg-slate-100 text-slate-700 border-slate-200',
                            ][$materialRequest->priority] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-extrabold border {{ $priorityColors }}">
                            {{ $materialRequest->priority }} Priority
                        </span>
                    </div>
                </div>

                {{-- Purpose / Notes --}}
                @if($materialRequest->purpose || $materialRequest->task)
                    <div class="col-span-1 md:col-span-2 lg:col-span-5 pt-4 border-t border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Purpose & Task Notes</span>
                        @if($materialRequest->task)
                            <p class="text-xs font-semibold text-blue-600 mb-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Task: {{ $materialRequest->task->name }} ({{ $materialRequest->task->task_code }})
                            </p>
                        @endif
                        <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100 font-medium">
                            {{ $materialRequest->purpose ?? 'No specific notes provided.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Procurement Info Banner if linked --}}
        @if($materialRequest->purchaseRequisition)
            <div class="bg-indigo-50/80 rounded-2xl border border-indigo-200 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-indigo-950">Converted to Purchase Requisition</h4>
                        <p class="text-xs text-indigo-700 mt-0.5">
                            Requisition Code: <strong>{{ $materialRequest->purchaseRequisition->code }}</strong> — Status: <span class="capitalize font-semibold">{{ $materialRequest->purchaseRequisition->status }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.procurement.requisitions.show', $materialRequest->purchaseRequisition) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-50 shadow-xs transition-colors">
                    View Requisition &rarr;
                </a>
            </div>
        @endif

        {{-- Requested Items Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Requested Items</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Materials requested for site delivery</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                    {{ $materialRequest->items->count() }} {{ Str::plural('item', $materialRequest->items->count()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Product / Material</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Quantity Requested</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Item Notes / Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($materialRequest->items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-3 text-sm">
                                    <div class="font-bold text-slate-900">{{ $item->product->name ?? 'Unknown Item' }}</div>
                                    <div class="text-xs text-slate-500 font-mono">SKU: {{ $item->product->sku ?? '—' }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm text-right font-extrabold text-slate-900">
                                    <span class="text-base">{{ number_format($item->quantity_requested, 2) }}</span>
                                    <span class="text-xs font-medium text-slate-500 ml-1">{{ $item->product->unit->name ?? ($item->product->unit->code ?? 'Unit') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $item->notes ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-500">
                                    No items listed in this request.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Convert to PR Confirmation Modal --}}
    <div x-data="{ open: false }"
         x-on:open-convert-pr-modal.window="open = true"
         x-on:keydown.escape.window="open = false">

        <template x-teleport="body">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">

                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="open = false"></div>

                {{-- Modal Content --}}
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100" @click.stop>
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <div class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                                Convert to Purchase Requisition
                            </div>
                            <button @click="open = false" type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">Convert Request {{ $materialRequest->request_number }} to PR?</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                                        This will create a new Purchase Requisition for procurement with all <strong>{{ $materialRequest->items->count() }} requested {{ Str::plural('item', $materialRequest->items->count()) }}</strong>. You can track its procurement status directly from this request.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 justify-end">
                            <button type="button" @click="open = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                                Cancel
                            </button>
                            <button type="button"
                                    @click="open = false; document.getElementById('convert-pr-form').submit();"
                                    class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Confirm Conversion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Cancel Request Confirmation Modal --}}
    <div x-data="{ open: false }"
         x-on:open-cancel-request-modal.window="open = true"
         x-on:keydown.escape.window="open = false">

        <template x-teleport="body">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">

                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="open = false"></div>

                {{-- Modal Content --}}
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100" @click.stop>
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <div class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                Cancel Material Request
                            </div>
                            <button @click="open = false" type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">Cancel Request {{ $materialRequest->request_number }}?</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                                        Are you sure you want to cancel this material request? This action will change the status to Cancelled and mark the request inactive.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 justify-end">
                            <button type="button" @click="open = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                                Keep Request
                            </button>
                            <button type="button"
                                    @click="open = false; document.getElementById('cancel-request-form').submit();"
                                    class="px-4 py-2 text-xs font-bold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                                Confirm Cancellation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
</x-layouts.admin>
