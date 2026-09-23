<x-layouts.admin title="Journal Entry Details">
    <x-slot:breadcrumbs>
        @php
            $journalNumber = $journal->journal_number ?? 'JE-' . str_pad($journal->id, 5, '0', STR_PAD_LEFT);
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => route('admin.finance.accounting.ledger.index')],
                ['label' => 'Journal Entries', 'url' => route('admin.finance.accounting.journals.index')],
                ['label' => $journalNumber]
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.finance.accounting.journals.index') }}" 
                   class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors shrink-0"
                   title="Back to Journal List">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>

                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                            Journal Entry #{{ $journalNumber }}
                        </h1>
                        @php
                            $statusConfig = match($journal->status) {
                                'Draft' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                                'Pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-400'],
                                'Posted' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                'Voided' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
                                default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $statusConfig['bg'] }}">
                            <span class="w-2 h-2 rounded-full {{ $statusConfig['dot'] }}"></span>
                            {{ $journal->status }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        {{ $journal->memo }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            @if($journal->status === 'Draft' || $journal->status === 'Pending')
                @can('post', $journal)
                    <form action="{{ route('admin.finance.accounting.journals.post', $journal) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal entry to the general ledger?');">
                        @csrf
                        <x-button type="success" submit class="shadow-sm hover:shadow-md transition-shadow">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Post to Ledger
                        </x-button>
                    </form>
                @endcan
            @endif

            @can('delete', $journal)
                @if($journal->status === 'Draft')
                    <form action="{{ route('admin.finance.accounting.journals.destroy', $journal) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this draft journal entry?');">
                        @csrf
                        @method('DELETE')
                        <x-button type="danger" submit class="shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </x-button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    {{-- Metrics Summary Grid --}}
    @php
        $totalDebit = $journal->entries->sum('debit');
        $totalCredit = $journal->entries->sum('credit');
        $isBalanced = round($totalDebit, 2) === round($totalCredit, 2) && $totalDebit > 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Total Debit</p>
                <p class="text-xl font-bold text-gray-900 mt-1">RWF {{ number_format($totalDebit, 2) }}</p>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Total Credit</p>
                <p class="text-xl font-bold text-gray-900 mt-1">RWF {{ number_format($totalCredit, 2) }}</p>
            </div>
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Balance Status</p>
                <div class="mt-1 flex items-center gap-1.5">
                    @if($isBalanced)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Balanced
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Unbalanced
                        </span>
                    @endif
                </div>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500">Line Items</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $journal->entries->count() }} Entries</p>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Main Content Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Metadata Panel --}}
        <x-card class="col-span-1 p-6 bg-white border border-gray-200/80 shadow-sm rounded-xl h-fit">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-3 mb-4">
                Journal Overview
            </h3>

            <div class="space-y-4 text-xs">
                @if($journal->company)
                    <div>
                        <span class="text-gray-500 block">Company</span>
                        <span class="font-semibold text-gray-900 mt-0.5 block">
                            {{ $journal->company->name }}
                        </span>
                    </div>
                @endif

                @if($journal->project)
                    <div>
                        <span class="text-gray-500 block">Project</span>
                        <a href="{{ route('admin.projects.show', $journal->project) }}" class="font-semibold text-blue-600 hover:underline mt-0.5 block">
                            {{ $journal->project->name }} ({{ $journal->project->project_code }})
                        </a>
                    </div>
                @endif

                <div>
                    <span class="text-gray-500 block">Transaction Date</span>
                    <span class="font-semibold text-gray-900 text-sm mt-0.5 block">
                        {{ $journal->date ? $journal->date->format('M d, Y') : '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-500 block">Reference Number</span>
                    <span class="font-mono font-medium text-gray-900 mt-0.5 block">
                        {{ $journal->reference_number ?: 'None' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-500 block">Fiscal Year</span>
                    <span class="font-semibold text-gray-900 mt-0.5 block">
                        {{ $journal->fiscalYear->name ?? 'N/A' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-500 block">Accounting Period</span>
                    <span class="font-semibold text-gray-900 mt-0.5 block">
                        {{ $journal->accountingPeriod->name ?? 'N/A' }}
                    </span>
                </div>

                @if($journal->branch)
                    <div>
                        <span class="text-gray-500 block">Branch</span>
                        <span class="font-semibold text-gray-900 mt-0.5 block">
                            {{ $journal->branch->name }}
                        </span>
                    </div>
                @endif

                <div class="pt-3 border-t border-gray-100 space-y-2">
                    <div class="flex justify-between text-gray-500">
                        <span>Created At:</span>
                        <span class="font-medium text-gray-700">{{ $journal->created_at ? $journal->created_at->format('M d, Y H:i') : '-' }}</span>
                    </div>
                    @if($journal->posted_at)
                        <div class="flex justify-between text-gray-500">
                            <span>Posted At:</span>
                            <span class="font-medium text-emerald-700">{{ $journal->posted_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Line Items Table Panel --}}
        <x-card class="col-span-1 lg:col-span-2 p-0 border border-gray-200/80 shadow-sm rounded-xl overflow-hidden">
            <div class="px-5 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Journal Lines & Allocations</h3>
                <span class="text-xs font-semibold text-gray-500">{{ $journal->entries->count() }} items</span>
            </div>

            <div class="overflow-x-auto min-w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/40 border-b border-gray-100 text-gray-500 text-[11px] font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Account Code & Name</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-xs sm:text-sm">
                        @foreach($journal->entries as $entry)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold text-blue-600 px-2 py-0.5 bg-blue-50 border border-blue-100 rounded">
                                                {{ $entry->chartOfAccount->code }}
                                            </span>
                                            <span class="font-semibold text-gray-900 text-xs">
                                                {{ $entry->chartOfAccount->name }}
                                            </span>
                                        </div>
                                        @if($entry->chartOfAccount->accountType)
                                            <span class="text-[10px] text-gray-400 mt-1">
                                                {{ $entry->chartOfAccount->accountType->name }} ({{ $entry->chartOfAccount->accountType->category }})
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-gray-600 text-xs">
                                    {{ $entry->description }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($entry->debit > 0)
                                        <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50/60 px-2.5 py-1 rounded-md border border-blue-100/60 inline-block">
                                            RWF {{ number_format($entry->debit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($entry->credit > 0)
                                        <span class="font-mono text-xs font-bold text-purple-700 bg-purple-50/60 px-2.5 py-1 rounded-md border border-purple-100/60 inline-block">
                                            RWF {{ number_format($entry->credit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="py-3.5 px-4 text-xs font-bold text-gray-900 text-right uppercase tracking-wider">
                                Total Verification:
                            </td>
                            <td class="py-3.5 px-4 text-xs font-bold text-blue-700 text-right font-mono">
                                RWF {{ number_format($totalDebit, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-xs font-bold text-purple-700 text-right font-mono">
                                RWF {{ number_format($totalCredit, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
