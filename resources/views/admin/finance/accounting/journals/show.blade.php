<x-layouts.admin title="Journal Entry Details">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Journal Entries', 'url' => route('admin.finance.accounting.journals.index')],
                ['label' => 'JE-' . str_pad($journal->id, 5, '0', STR_PAD_LEFT)]
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                Journal Entry #JE-{{ str_pad($journal->id, 5, '0', STR_PAD_LEFT) }}
                @php
                    $statusType = match($journal->status) {
                        'Draft' => 'default',
                        'Pending' => 'warning',
                        'Posted' => 'success',
                        'Voided' => 'danger',
                        default => 'default',
                    };
                @endphp
                <x-badge :type="$statusType" class="ml-3">{{ $journal->status }}</x-badge>
            </h1>
            <p class="mt-1 text-sm text-gray-500">{{ $journal->memo }}</p>
        </div>
        
        <div class="flex items-center gap-2">
            @if($journal->status === 'Draft' || $journal->status === 'Pending')
                <form action="{{ route('admin.finance.accounting.journals.post', $journal) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal entry to the ledger?');">
                    @csrf
                    <x-button type="success" submit>
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Post to Ledger
                    </x-button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-card class="col-span-1">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2 mb-3">Details</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="text-sm font-medium text-gray-900">{{ $journal->date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Reference Number</p>
                    <p class="text-sm font-medium text-gray-900">{{ $journal->reference_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Fiscal Period</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $journal->accountingPeriod->name ?? 'None' }} 
                        ({{ $journal->fiscalYear->name ?? 'None' }})
                    </p>
                </div>
            </div>
        </x-card>
        
        <x-card class="col-span-1 md:col-span-2 p-0 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Lines</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100">
                            <th class="py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="py-2 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                            <th class="py-2 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp
                        @foreach($journal->entries as $entry)
                            @php
                                $totalDebit += $entry->debit;
                                $totalCredit += $entry->credit;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                                    {{ $entry->chartOfAccount->code }} - {{ $entry->chartOfAccount->name }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">
                                    {{ $entry->description }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 text-right">
                                    {{ $entry->debit > 0 ? '$' . number_format($entry->debit, 2) : '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 text-right">
                                    {{ $entry->credit > 0 ? '$' . number_format($entry->credit, 2) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="py-3 px-4 text-sm font-bold text-gray-900 text-right">Total:</td>
                            <td class="py-3 px-4 text-sm font-bold text-gray-900 text-right">${{ number_format($totalDebit, 2) }}</td>
                            <td class="py-3 px-4 text-sm font-bold text-gray-900 text-right">${{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
