<x-layouts.admin title="Invoice {{ $invoice->invoice_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Invoices', 'url' => route('admin.finance.invoices.index')],
                ['label' => $invoice->invoice_number]
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
                @php
                    $statusType = match($invoice->status) {
                        'Draft' => 'default',
                        'Issued' => 'primary',
                        'Pending Approval' => 'warning',
                        'Partially Paid' => 'warning',
                        'Paid' => 'success',
                        'Overdue' => 'danger',
                        'Cancelled' => 'default',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType">{{ $invoice->status }}</x-badge>
            </div>
            @if($invoice->client)
                <p class="mt-1 text-sm text-gray-500">Client: {{ $invoice->client->name }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-2 print-hide">


            @if($invoice->status === 'Draft')
                @can('update', $invoice)
                    <form method="POST" action="{{ route('admin.finance.invoices.issue', $invoice) }}" class="inline" onsubmit="return confirm('Are you sure you want to issue this invoice?');">
                        @csrf
                        @method('PATCH')
                        <x-button type="primary" size="sm" submit>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Issue Invoice
                        </x-button>
                    </form>

                    <x-button type="ghost" size="sm" href="{{ route('admin.finance.invoices.edit', $invoice) }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </x-button>
                @endcan
            @endif

            <x-button type="ghost" size="sm" onclick="window.print()">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </x-button>

            @if($invoice->balance_due > 0 && !in_array($invoice->status, ['Draft', 'Cancelled', 'Pending Approval']))
                @can('create', App\Models\Payment::class)
                    <x-button type="primary" size="sm" href="{{ route('admin.finance.payments.create', ['invoice_id' => $invoice->id]) }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Record Payment
                    </x-button>
                @endcan
            @endif

            @if(!in_array($invoice->status, ['Cancelled', 'Draft']))
                @can('update', $invoice)
                    <form method="POST" action="{{ route('admin.finance.invoices.cancel', $invoice) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                        @csrf
                        @method('PATCH')
                        <x-button type="danger" size="sm" submit>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Invoice
                        </x-button>
                    </form>
                @endcan
            @endif
        </div>
    </div>
    
    @if($invoice->approval)
        <div class="mb-6 print-hide">
            <x-card class="bg-blue-50 border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-900">Approval Workflow</h3>
                            <p class="text-sm text-blue-700 mt-1">This invoice is currently: <strong>{{ $invoice->approval->status }}</strong>.</p>
                        </div>
                    </div>
                    <x-button type="primary" size="sm" href="{{ route('admin.approvals.show', $invoice->approval) }}">
                        View Approval Details
                    </x-button>
                </div>
            </x-card>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-3 space-y-6">
            <x-card class="print-friendly p-8">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-12 border-b border-gray-100 pb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">INVOICE</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $invoice->company->name ?? 'Creative ERP' }}</p>
                        @if(isset($invoice->company->address))
                            <p>{!! nl2br(e($invoice->company->address)) !!}</p>
                        @endif
                    </div>
                </div>

                {{-- Parties Info --}}
                <div class="grid grid-cols-2 gap-12 mb-12">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Bill To:</h3>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $invoice->client->name ?? 'Unknown Client' }}
                        </p>
                        @if($invoice->client && $invoice->client->billing_address)
                            <p class="text-sm text-gray-600 mt-1">{!! nl2br(e($invoice->client->billing_address)) !!}</p>
                        @endif
                    </div>

                    <div>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Issue Date</span>
                                <span class="font-medium text-gray-900">{{ $invoice->issue_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Due Date</span>
                                <span class="font-medium {{ $invoice->due_date->isPast() && $invoice->balance_due > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </span>
                            </div>
                            @if($invoice->project)
                            <div class="col-span-2">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Project</span>
                                <span class="font-medium text-gray-900">{{ $invoice->project->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="mb-8 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-y border-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">Description</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Qty</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit Price</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $item->description }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-600 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-4 px-4 text-sm font-semibold text-gray-900 text-right">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals Area --}}
                <div class="flex justify-end mb-12">
                    <div class="w-full sm:w-1/2 lg:w-1/3">
                        <div class="bg-gray-50 rounded-xl p-5 space-y-3 border border-gray-200">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium">${{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            
                            @if($invoice->tax_total > 0)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Tax</span>
                                    <span class="font-medium">${{ number_format($invoice->tax_total, 2) }}</span>
                                </div>
                            @endif

                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-900">Total Amount</span>
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($invoice->total_amount, 2) }}</span>
                                </div>
                            </div>
                            
                            @if($invoice->paid_amount > 0)
                                <div class="flex justify-between items-center text-sm text-green-600 pt-2 border-t border-gray-200 mt-2">
                                    <span>Amount Paid</span>
                                    <span class="font-medium">-${{ number_format($invoice->paid_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center bg-gray-900 text-white p-3 rounded-lg mt-3">
                                    <span class="text-base font-bold">Balance Due</span>
                                    <span class="text-xl font-black">${{ number_format($invoice->balance_due, 2) }}</span>
                                </div>
                            @else
                                <div class="flex justify-between items-center bg-gray-900 text-white p-3 rounded-lg mt-3">
                                    <span class="text-base font-bold">Balance Due</span>
                                    <span class="text-xl font-black">${{ number_format($invoice->balance_due, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($invoice->notes)
                    <div class="border-t border-gray-200 pt-8 mt-8">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Notes</h4>
                        <div class="text-sm text-gray-600 prose prose-sm max-w-none">
                            {!! nl2br(e($invoice->notes)) !!}
                        </div>
                    </div>
                @endif
            </x-card>
            
            {{-- Payments History --}}
            @if($invoice->allocations->count() > 0)
                <div class="print-hide">
                    <x-card>
                        <x-slot:header>Payment History</x-slot:header>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Ref</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Applied</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($invoice->allocations as $allocation)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $allocation->payment->payment_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-blue-600">
                                                <a href="{{ route('admin.finance.payments.show', $allocation->payment) }}">
                                                    {{ $allocation->payment->reference_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $allocation->payment->paymentMethod->name ?? 'Unknown' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                                ${{ number_format($allocation->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            @endif
        </div>

        {{-- Sidebar: Activity & Info --}}
        <div class="space-y-6 print-hide">
            <x-card>
                <x-slot:header>Information</x-slot:header>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Created</span>
                        <span class="text-sm text-gray-900">{{ $invoice->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($invoice->quotation_id)
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Related Quotation</span>
                            <a href="{{ route('admin.crm.quotations.show', $invoice->quotation_id) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                View Quotation
                            </a>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <style>
        @media print {
            aside, nav, .breadcrumbs, .print-hide, form {
                display: none !important;
            }
            .print-friendly {
                box-shadow: none !important;
                border: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
            }
        }
    </style>
</x-layouts.admin>
