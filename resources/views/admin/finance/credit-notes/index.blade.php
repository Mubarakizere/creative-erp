<x-layouts.admin title="Credit Notes">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Finance', 'url' => '#'],
                ['label' => 'Credit Notes']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div x-data="{ deleteAction: '', deleteLabel: '' }">
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

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.finance.credit-notes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Credit note #, client, invoice, reason..." class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm min-h-[42px] pl-9">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white min-h-[42px]">
                    <option value="">All statuses</option>
                    @foreach(['Draft', 'Issued', 'Applied', 'Refunded', 'Cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm flex-1 min-h-[42px]">Search</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.finance.credit-notes.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 border border-gray-200 min-h-[42px]">Clear</a>
                @endif
            </div>
        </form>
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
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount (RWF)</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining (RWF)</th>
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
                                RWF {{ number_format($note->amount, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900 text-right">
                                RWF {{ number_format($note->remaining_balance, 2) }}
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
                                @php
                                    $statusType = match($note->status) {
                                        'Issued' => 'success',
                                        'Applied' => 'default',
                                        'Refunded' => 'primary',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ $note->status }}</x-badge>
                            </td>
                            <td class="py-4 px-4 text-sm text-right">
                                <x-action-dropdown>
                                    @can('view', $note)
                                        <x-action-dropdown-item href="{{ route('admin.finance.credit-notes.show', $note) }}" icon="view">
                                            View Details
                                        </x-action-dropdown-item>
                                    @endcan

                                    @can('delete', $note)
                                        <x-action-dropdown-item
                                            data-delete-url="{{ route('admin.finance.credit-notes.destroy', $note) }}"
                                            data-credit-note="{{ $note->credit_note_number }}"
                                            @click="deleteAction = $el.dataset.deleteUrl; deleteLabel = $el.dataset.creditNote; $dispatch('open-modal', 'delete-credit-note')"
                                            icon="delete" variant="danger">
                                            Delete Note
                                        </x-action-dropdown-item>
                                    @endcan
                                </x-action-dropdown>
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
    <x-modal id="delete-credit-note" maxWidth="md">
        <x-slot:header>Delete Credit Note</x-slot:header>
        <div class="text-center py-4">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete credit note <span x-text="deleteLabel"></span>?</h3>
            <p class="text-sm text-gray-500">The note will be removed from the active list. A credit note that has been applied or refunded cannot be deleted.</p>
        </div>
        <x-slot:footer>
            <div class="flex items-center gap-3 w-full justify-end">
                <button type="button" @click="open = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 shadow-sm">Cancel</button>
                <form method="POST" :action="deleteAction" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-sm">Delete Credit Note</button>
                </form>
            </div>
        </x-slot:footer>
    </x-modal>
    </div>
</x-layouts.admin>
