<x-layouts.admin title="Invoices">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Invoices']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
            <p class="mt-1 text-sm text-gray-500">Manage customer invoices and payments.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('create', App\Models\Invoice::class)
                <x-button type="primary" href="{{ route('admin.finance.invoices.create') }}">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Invoice
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Filter/Search --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.finance.invoices.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto flex-1 max-w-xs">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Issued" {{ request('status') === 'Issued' ? 'selected' : '' }}>Issued</option>
                    <option value="Partially Paid" {{ request('status') === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="w-full sm:w-auto">
                <x-button type="default" href="{{ route('admin.finance.invoices.index') }}">Clear</x-button>
            </div>
        </form>
    </x-card>

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Issue Date</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.finance.invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900">
                                {{ $invoice->client->name ?? 'Unknown Client' }}
                                @if($invoice->project)
                                    <span class="block text-xs text-gray-500">{{ $invoice->project->name }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                <span class="{{ $invoice->due_date->isPast() && !in_array($invoice->status, ['Paid', 'Cancelled']) ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                ${{ number_format($invoice->total_amount, 2) }}
                                @if($invoice->balance_due > 0 && $invoice->status !== 'Draft')
                                    <span class="block text-xs text-red-500 font-normal">Balance: ${{ number_format($invoice->balance_due, 2) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
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
                                <x-badge :type="$statusType">{{ $invoice->status }}</x-badge>
                            </td>
                            <td class="py-4 px-4 text-sm text-right">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1"
                                         style="display: none;">

                                        <a href="{{ route('admin.finance.invoices.show', $invoice) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>

                                        @if($invoice->status === 'Draft' && auth()->user()->can('update', $invoice))
                                            <a href="{{ route('admin.finance.invoices.edit', $invoice) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
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
                            </td>
                        </tr>

                        {{-- Modals --}}
                        @if($invoice->status === 'Draft' && auth()->user()->can('update', $invoice))
                            <x-modal id="issue-invoice-{{ $invoice->id }}" maxWidth="md">
                                <x-slot:header>Issue Invoice</x-slot:header>
                                <div class="text-center py-4">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Issue Invoice {{ $invoice->invoice_number }}?</h3>
                                    <p class="text-sm text-gray-500">This will move the invoice from Draft to Issued, allowing payments to be recorded against it.</p>
                                </div>
                                <x-slot:footer>
                                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                                    <form method="POST" action="{{ route('admin.finance.invoices.issue', $invoice) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <x-button type="primary" submit>Issue Invoice</x-button>
                                    </form>
                                </x-slot:footer>
                            </x-modal>
                        @endif

                        @can('delete', $invoice)
                            <x-modal id="delete-invoice-{{ $invoice->id }}" maxWidth="md">
                                <x-slot:header>Delete Invoice</x-slot:header>
                                <div class="text-center py-4">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Invoice {{ $invoice->invoice_number }}?</h3>
                                    <p class="text-sm text-gray-500">This action will permanently delete the invoice. This cannot be undone.</p>
                                </div>
                                <x-slot:footer>
                                    <x-button type="ghost" @click="open = false">Cancel</x-button>
                                    <form method="POST" action="{{ route('admin.finance.invoices.destroy', $invoice) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="danger" submit>Delete Invoice</x-button>
                                    </form>
                                </x-slot:footer>
                            </x-modal>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new invoice.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($invoices->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
