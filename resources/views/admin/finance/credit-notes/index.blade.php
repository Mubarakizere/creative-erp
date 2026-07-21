<x-layouts.admin title="Credit Notes">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Credit Notes']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Credit Notes</h1>
            <p class="mt-1 text-sm text-gray-500">Manage credit notes issued to clients.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @can('create', App\Models\CreditNote::class)
                <x-button type="primary" href="{{ route('admin.finance.credit-notes.create') }}">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Issue Credit Note
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ref #</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Issue Date</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($creditNotes as $note)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.finance.credit-notes.show', $note) }}">{{ $note->credit_note_number }}</a>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900">
                                {{ $note->client->name ?? 'Unknown' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $note->issue_date->format('M d, Y') }}</td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                ${{ number_format($note->amount, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                ${{ number_format($note->remaining_balance, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
                                @php
                                    $statusType = match($note->status) {
                                        'Open' => 'success',
                                        'Applied' => 'default',
                                        'Refunded' => 'primary',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ $note->status }}</x-badge>
                            </td>
                            <td class="py-4 px-4 text-sm text-right space-x-2">
                                <a href="{{ route('admin.finance.credit-notes.show', $note) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No credit notes found</h3>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($creditNotes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $creditNotes->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.admin>
