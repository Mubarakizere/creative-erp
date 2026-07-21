<x-layouts.admin title="Credit Note {{ $creditNote->credit_note_number }}">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Credit Notes', 'url' => route('admin.finance.credit-notes.index')],
                ['label' => $creditNote->credit_note_number]
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Credit Note: {{ $creditNote->credit_note_number }}</h1>
                @php
                    $statusType = match($creditNote->status) {
                        'Open' => 'success',
                        'Applied' => 'default',
                        'Refunded' => 'primary',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType">{{ $creditNote->status }}</x-badge>
            </div>
            @if($creditNote->client)
                <p class="mt-1 text-sm text-gray-500">Issued to: {{ $creditNote->client->name }} on {{ $creditNote->issue_date->format('M d, Y') }}</p>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-2 print-hide">
            <x-button type="ghost" size="sm" onclick="window.print()">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </x-button>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 p-4 rounded-md">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 bg-green-50 p-4 rounded-md">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Main Document Area --}}
        <div class="xl:col-span-2 space-y-6">
            <x-card class="print-friendly p-8">
                {{-- Header / Logos --}}
                <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase">CREDIT NOTE</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ $creditNote->credit_note_number }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $creditNote->company->name ?? 'Creative ERP' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-12 mb-12">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Credit To:</h3>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $creditNote->client->name ?? 'Unknown Client' }}
                        </p>
                    </div>

                    <div>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Date</span>
                                <span class="font-medium text-gray-900">{{ $creditNote->issue_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Original Amount</span>
                                <span class="font-medium text-gray-900">${{ number_format($creditNote->amount, 2) }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Remaining Balance</span>
                                <span class="text-lg font-bold text-green-600">${{ number_format($creditNote->remaining_balance, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="mb-12">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Reason for Credit:</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        {!! nl2br(e($creditNote->reason)) !!}
                    </p>
                </div>
            </x-card>
        </div>

        {{-- Sidebar: Info & Actions --}}
        <div class="space-y-6 print-hide">
            @if($creditNote->remaining_balance > 0 && $openInvoices->count() > 0)
                <x-card>
                    <x-slot:header>Apply Credit</x-slot:header>
                    <form method="POST" action="{{ route('admin.finance.credit-notes.apply', $creditNote) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Apply to Invoice</label>
                            <select name="invoice_id" id="invoice_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Invoice</option>
                                @foreach($openInvoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_number }} (Balance: ${{ number_format($inv->balance_due, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount to Apply</label>
                            <input type="number" name="amount" id="amount" required min="0.01" max="{{ $creditNote->remaining_balance }}" step="0.01" value="{{ $creditNote->remaining_balance }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <x-button type="primary" class="w-full justify-center" submit>Apply to Invoice</x-button>
                    </form>
                </x-card>
            @elseif($creditNote->remaining_balance > 0)
                <x-card>
                    <x-slot:header>Apply Credit</x-slot:header>
                    <p class="text-sm text-gray-500">This client has no open invoices. You can issue a refund instead.</p>
                    <div class="mt-4">
                        <x-button type="default" class="w-full justify-center" href="{{ route('admin.finance.refunds.create') }}">Issue Refund</x-button>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot:header>Information</x-slot:header>
                <div class="space-y-4">
                    @if($creditNote->invoice_id)
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Related Invoice</span>
                            <a href="{{ route('admin.finance.invoices.show', $creditNote->invoice_id) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                {{ $creditNote->invoice->invoice_number ?? 'View Invoice' }}
                            </a>
                        </div>
                    @endif
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Created On</span>
                        <span class="text-sm text-gray-900">{{ $creditNote->created_at->format('M d, Y h:i A') }}</span>
                    </div>
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
