<x-layouts.admin title="Journal Entries">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Journal Entries']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Journal Entries</h1>
            <p class="mt-1 text-sm text-gray-500">Manage manual and automated journal entries in the ledger.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <x-button type="primary" href="{{ route('admin.finance.accounting.journals.create') }}">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Journal Entry
            </x-button>
        </div>
    </div>

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Journal #</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Memo</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.finance.accounting.journals.show', $journal) }}">JE-{{ str_pad($journal->id, 5, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                                {{ $journal->date->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ \Illuminate\Support\Str::limit($journal->memo, 40) }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ $journal->reference_number ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
                                @php
                                    $statusType = match($journal->status) {
                                        'Draft' => 'default',
                                        'Pending' => 'warning',
                                        'Posted' => 'success',
                                        'Voided' => 'danger',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ $journal->status }}</x-badge>
                            </td>
                            <td class="py-4 px-4 text-sm text-right flex justify-end space-x-2">
                                <a href="{{ route('admin.finance.accounting.journals.show', $journal) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($journal->status === 'Draft' || $journal->status === 'Pending')
                                    <form action="{{ route('admin.finance.accounting.journals.post', $journal) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal entry to the ledger?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Post to Ledger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No journal entries found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a manual journal entry.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($journals->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $journals->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
