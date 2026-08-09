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

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.finance.payments.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payment: {{ $payment->reference_number }}</h1>
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider leading-5 bg-green-100 text-green-800 border border-green-200">
                    Completed
                </span>
            </div>
            @if($payment->client)
                <p class="mt-1.5 text-sm text-gray-500 font-medium">Received from: <span class="text-gray-700">{{ $payment->client->name }}</span> on {{ $payment->payment_date->format('M d, Y') }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-3 print-hide">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Receipt
            </button>

            @can('delete', $payment)
                <div x-data="{ open: false }" class="inline-block relative">
                    <button @click="open = true" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Payment
                    </button>

                    <x-modal id="delete-payment-{{ $payment->id }}" maxWidth="md">
                        <x-slot:header>Delete Payment</x-slot:header>
                        <div class="text-center py-4 whitespace-normal">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Payment {{ $payment->reference_number }}?</h3>
                            <p class="text-sm text-gray-500">This will permanently delete the payment and un-allocate the amounts from the associated invoices. This action cannot be undone.</p>
                        </div>
                        <x-slot:footer>
                            <div class="flex items-center gap-3 w-full justify-end">
                                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">Cancel</button>
                                <form method="POST" action="{{ route('admin.finance.payments.destroy', $payment) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Delete Payment</button>
                                </form>
                            </div>
                        </x-slot:footer>
                    </x-modal>
                    
                    <button x-show="!open" @click="$dispatch('open-modal', 'delete-payment-{{ $payment->id }}')" type="button" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></button>
                </div>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm print-friendly p-8 lg:p-10">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">PAYMENT RECEIPT</h2>
                        @if($payment->receipt)
                            <p class="text-sm font-medium text-gray-500 mt-1">Receipt #: <span class="text-gray-900">{{ $payment->receipt->receipt_number }}</span></p>
                        @endif
                        <p class="text-sm font-medium text-gray-500 mt-1">Payment Ref: <span class="text-gray-900">{{ $payment->reference_number }}</span></p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-bold text-gray-900 text-lg tracking-tight">{{ $payment->company->name ?? 'Creative Century Engineering' }}</p>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 mb-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-blue-500 uppercase tracking-widest mb-1.5">Amount Received</p>
                        <p class="text-4xl font-black text-blue-900 tracking-tight">RWF {{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 text-right">
                        <p class="text-sm font-medium text-blue-800">Date: <span class="font-bold text-blue-900">{{ $payment->payment_date->format('F d, Y') }}</span></p>
                        <p class="text-sm font-medium text-blue-800 mt-1.5">Method: <span class="font-bold text-blue-900">{{ $payment->paymentMethod->name ?? 'Unknown' }}</span></p>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Received From:</h3>
                    <p class="text-xl font-bold text-gray-900">{{ $payment->client->name ?? 'Unknown Client' }}</p>
                    @if($payment->client && $payment->client->email)
                        <p class="text-sm text-gray-500 font-medium mt-1">{{ $payment->client->email }}</p>
                    @endif
                </div>

                {{-- Allocations Table --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-3">Payment Applied To</h3>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full text-left divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Invoice #</th>
                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Issue Date</th>
                                    <th class="py-3 px-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Invoice Total</th>
                                    <th class="py-3 px-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Amount Applied</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($payment->allocations as $allocation)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-4 px-4 text-sm font-bold text-blue-600">
                                            <a href="{{ route('admin.finance.invoices.show', $allocation->invoice_id) }}" class="print-hide hover:underline">
                                                {{ $allocation->invoice->invoice_number ?? 'Unknown' }}
                                            </a>
                                            <span class="hidden print:inline">{{ $allocation->invoice->invoice_number ?? 'Unknown' }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-sm font-medium text-gray-600">{{ optional($allocation->invoice->issue_date)->format('M d, Y') }}</td>
                                        <td class="py-4 px-4 text-sm font-medium text-gray-600 text-right">RWF {{ number_format($allocation->invoice->total_amount ?? 0, 2) }}</td>
                                        <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">RWF {{ number_format($allocation->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 px-4 text-sm text-center text-gray-500 font-medium">No invoices allocated. This payment is unapplied.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50/80 border-t border-gray-200">
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-sm font-bold text-gray-900 text-right">Total Applied</td>
                                    <td class="py-4 px-4 text-base font-black text-blue-600 text-right tracking-tight">RWF {{ number_format($payment->allocations->sum('amount'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Notes --}}
                @if($payment->notes)
                    <div class="border-t border-gray-100 pt-6 mt-8">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Notes</h4>
                        <div class="text-sm font-medium text-gray-600 prose prose-sm max-w-none bg-gray-50 rounded-xl p-4 border border-gray-100">
                            {!! nl2br(e($payment->notes)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: Info --}}
        <div class="space-y-6 print-hide">
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Transaction Info</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Created On</span>
                        <span class="text-sm font-medium text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Created By</span>
                        <span class="text-sm font-medium text-gray-900 flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold mr-2">
                                {{ substr($payment->creator->name ?? 'S', 0, 1) }}
                            </div>
                            {{ $payment->creator->name ?? 'System' }}
                        </span>
                    </div>
                    @if($payment->bankAccount)
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Deposited To</span>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 mt-1">
                            <span class="block text-sm font-bold text-gray-900">{{ $payment->bankAccount->bank_name }}</span>
                            <span class="block text-xs font-medium text-gray-500 font-mono mt-0.5">{{ $payment->bankAccount->account_number }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
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
