<x-layouts.admin title="Trial Balance & Ledger">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'General Ledger']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Trial Balance</h1>
            <p class="mt-1 text-sm text-gray-500">View account balances for the selected fiscal year.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <x-button type="default" onclick="window.print()">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </x-button>
        </div>
    </div>

    {{-- Filter/Search --}}
    <x-financial-filter-bar 
        action="{{ route('admin.finance.accounting.ledger.index') }}"
        :fiscalYears="$fiscalYears"
        :fiscalYearId="$fiscalYearId"
        :branches="$branches"
        :departments="$departments"
        :projects="$projects"
        :clients="$clients"
        :filters="$filters"
    />

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Code</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Name</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trialBalance['accounts'] as $account)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                                {{ $account['code'] }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                {{ $account['name'] }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 text-right">
                                {{ $account['balance'] > 0 ? '$' . number_format($account['balance'], 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 text-right">
                                {{ $account['balance'] < 0 ? '$' . number_format(abs($account['balance']), 2) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">
                                <p class="text-sm">No ledger entries found for the selected fiscal year.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($trialBalance['accounts']) > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td colspan="2" class="py-4 px-4 text-sm font-bold text-gray-900 text-right uppercase tracking-wider">Totals:</td>
                            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right border-double border-b-4 border-gray-300">
                                ${{ number_format($trialBalance['total_debits'], 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right border-double border-b-4 border-gray-300">
                                ${{ number_format($trialBalance['total_credits'], 2) }}
                            </td>
                        </tr>
                        @if(abs($trialBalance['total_debits'] - $trialBalance['total_credits']) > 0.01)
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-center text-sm font-medium text-red-600 bg-red-50">
                                    Warning: Trial balance is not balanced. Difference: ${{ number_format(abs($trialBalance['total_debits'] - $trialBalance['total_credits']), 2) }}
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                @endif
            </table>
        </div>
    </x-card>
</x-layouts.admin>
