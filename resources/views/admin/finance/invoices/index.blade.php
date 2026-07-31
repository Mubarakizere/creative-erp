<x-layouts.admin title="Invoices">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Invoices']
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Invoice::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Invoices</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage customer invoices and payments.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @can('create', App\Models\Invoice::class)
                <a href="{{ route('admin.finance.invoices.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New Invoice
                </a>
            @endcan
        </div>
    </div>

    {{-- Filter/Search --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.finance.invoices.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto flex-1 max-w-xs">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="relative">
                    <select name="status" id="status" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors bg-white min-h-[42px] pl-3 pr-10" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Issued" {{ request('status') === 'Issued' ? 'selected' : '' }}>Issued</option>
                        <option value="Partially Paid" {{ request('status') === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div class="w-full sm:w-auto">
                <a href="{{ route('admin.finance.invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200 w-full justify-center sm:w-auto min-h-[42px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Invoice #</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Client</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Dates</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Amount</th>
                        <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors">{{ $invoice->invoice_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->client->name ?? 'Unknown Client' }}</div>
                                @if($invoice->project)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $invoice->project->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm text-gray-500">Issued: <span class="font-medium text-gray-700">{{ $invoice->issue_date->format('M d, Y') }}</span></div>
                                <div class="text-xs mt-0.5 {{ $invoice->due_date->isPast() && !in_array($invoice->status, ['Paid', 'Cancelled']) ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    Due: {{ $invoice->due_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">RWF {{ number_format($invoice->total_amount, 2) }}</div>
                                @if($invoice->balance_due > 0 && $invoice->status !== 'Draft')
                                    <div class="text-xs text-red-500 mt-0.5 font-medium">Bal: RWF {{ number_format($invoice->balance_due, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusType = match($invoice->status) {
                                        'Draft' => 'default',
                                        'Issued' => 'primary',
                                        'Partially Paid' => 'warning',
                                        'Paid' => 'success',
                                        'Overdue' => 'danger',
                                        'Cancelled' => 'default',
                                        default => 'default',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold leading-5 
                                    {{ $statusType === 'default' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $statusType === 'primary' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $statusType === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $statusType === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $statusType === 'danger' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1" style="display: none;">
                                        <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>

                                        @if($invoice->status === 'Draft' && auth()->user()->can('update', $invoice))
                                            <a href="{{ route('admin.finance.invoices.edit', $invoice) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>

                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button @click="open = false; $dispatch('open-modal', 'issue-invoice-{{ $invoice->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Issue
                                            </button>
                                        @endif

                                        @if($invoice->balance_due > 0 && !in_array($invoice->status, ['Draft', 'Cancelled']))
                                            @can('create', App\Models\Payment::class)
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <a href="{{ route('admin.finance.payments.create', ['invoice_id' => $invoice->id]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Record Payment
                                                </a>
                                            @endcan
                                        @endif

                                        @can('delete', $invoice)
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button @click="open = false; $dispatch('open-modal', 'delete-invoice-{{ $invoice->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors font-bold">
                                                <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Delete
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                {{-- Modals --}}
                                @if($invoice->status === 'Draft' && auth()->user()->can('update', $invoice))
                                    <x-modal id="issue-invoice-{{ $invoice->id }}" maxWidth="md">
                                        <x-slot:header>Issue Invoice</x-slot:header>
                                        <div class="text-center py-4 whitespace-normal">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4 border border-green-200">
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Issue Invoice {{ $invoice->invoice_number }}?</h3>
                                            <p class="text-sm text-gray-500">This will move the invoice from Draft to Issued, allowing payments to be recorded against it.</p>
                                        </div>
                                        <x-slot:footer>
                                            <div class="flex items-center gap-3 w-full justify-end">
                                                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                <form method="POST" action="{{ route('admin.finance.invoices.issue', $invoice) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors shadow-sm">Issue Invoice</button>
                                                </form>
                                            </div>
                                        </x-slot:footer>
                                    </x-modal>
                                @endif

                                @can('delete', $invoice)
                                    <x-modal id="delete-invoice-{{ $invoice->id }}" maxWidth="md">
                                        <x-slot:header>Delete Invoice</x-slot:header>
                                        <div class="text-center py-4 whitespace-normal">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Invoice {{ $invoice->invoice_number }}?</h3>
                                            <p class="text-sm text-gray-500">This action will permanently delete the invoice. This cannot be undone.</p>
                                        </div>
                                        <x-slot:footer>
                                            <div class="flex items-center gap-3 w-full justify-end">
                                                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                                <form method="POST" action="{{ route('admin.finance.invoices.destroy', $invoice) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Invoice</button>
                                                </form>
                                            </div>
                                        </x-slot:footer>
                                    </x-modal>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No invoices found</h3>
                                <p class="text-sm text-gray-500 font-medium">Get started by creating a new invoice.</p>
                                @can('create', App\Models\Invoice::class)
                                    <a href="{{ route('admin.finance.invoices.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        New Invoice
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $invoices->withQueryString()->links('components.pagination') }}
            </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view invoices.</p>
    </div>
    @endcan
</x-layouts.admin>
