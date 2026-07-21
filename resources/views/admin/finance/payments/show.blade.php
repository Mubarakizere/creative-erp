<x-layouts.admin title="Payment {{ $payment->reference_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Payments', 'url' => route('admin.finance.payments.index')],
                ['label' => $payment->reference_number]
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Payment: {{ $payment->reference_number }}</h1>
                <x-badge type="success">Completed</x-badge>
            </div>
            @if($payment->client)
                <p class="mt-1 text-sm text-gray-500">Received from: {{ $payment->client->name }} on {{ $payment->payment_date->format('M d, Y') }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-2 print-hide">
            <x-button type="ghost" size="sm" onclick="window.print()">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Receipt
            </x-button>

            @can('delete', $payment)
                <form method="POST" action="{{ route('admin.finance.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment? This will un-allocate the amounts from the associated invoices.');">
                    @csrf
                    @method('DELETE')
                    <x-button type="danger" size="sm" submit>
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Payment
                    </x-button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-2 space-y-6">
            <x-card class="print-friendly p-8">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">PAYMENT RECEIPT</h2>
                        @if($payment->receipt)
                            <p class="text-sm font-medium text-gray-500 mt-1">Receipt #: {{ $payment->receipt->receipt_number }}</p>
                        @endif
                        <p class="text-sm font-medium text-gray-500 mt-1">Payment Ref: {{ $payment->reference_number }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $payment->company->name ?? 'Creative ERP' }}</p>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-blue-500 uppercase tracking-wider mb-1">Amount Received</p>
                        <p class="text-4xl font-black text-blue-900">${{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 text-right">
                        <p class="text-sm font-medium text-blue-800">Date: <span class="font-bold">{{ $payment->payment_date->format('F d, Y') }}</span></p>
                        <p class="text-sm font-medium text-blue-800 mt-1">Method: <span class="font-bold">{{ $payment->paymentMethod->name ?? 'Unknown' }}</span></p>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Received From:</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $payment->client->name ?? 'Unknown Client' }}</p>
                    @if($payment->client && $payment->client->email)
                        <p class="text-sm text-gray-600">{{ $payment->client->email }}</p>
                    @endif
                </div>

                {{-- Allocations Table --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2">Payment Applied To</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="py-2 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Invoice #</th>
                                    <th class="py-2 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wider">Issue Date</th>
                                    <th class="py-2 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Invoice Total</th>
                                    <th class="py-2 px-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount Applied</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($payment->allocations as $allocation)
                                    <tr>
                                        <td class="py-3 px-4 text-sm font-medium text-blue-600">
                                            <a href="{{ route('admin.finance.invoices.show', $allocation->invoice_id) }}" class="print-hide">
                                                {{ $allocation->invoice->invoice_number ?? 'Unknown' }}
                                            </a>
                                            <span class="hidden print:inline">{{ $allocation->invoice->invoice_number ?? 'Unknown' }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ optional($allocation->invoice->issue_date)->format('M d, Y') }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 text-right">${{ number_format($allocation->invoice->total_amount ?? 0, 2) }}</td>
                                        <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">${{ number_format($allocation->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 text-sm text-center text-gray-500">No invoices allocated. This payment is unapplied.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="3" class="py-3 px-4 text-sm font-bold text-gray-900 text-right">Total Applied</td>
                                    <td class="py-3 px-4 text-sm font-black text-gray-900 text-right">${{ number_format($payment->allocations->sum('amount'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Notes --}}
                @if($payment->notes)
                    <div class="border-t border-gray-200 pt-6 mt-8">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Notes</h4>
                        <div class="text-sm text-gray-600 prose prose-sm max-w-none">
                            {!! nl2br(e($payment->notes)) !!}
                        </div>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Sidebar: Info --}}
        <div class="space-y-6 print-hide">
            <x-card>
                <x-slot:header>Transaction Info</x-slot:header>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Created On</span>
                        <span class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Created By</span>
                        <span class="text-sm text-gray-900">{{ $payment->creator->name ?? 'System' }}</span>
                    </div>
                    @if($payment->bankAccount)
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Deposited To</span>
                        <span class="text-sm text-gray-900">{{ $payment->bankAccount->bank_name }} - {{ $payment->bankAccount->account_number }}</span>
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
