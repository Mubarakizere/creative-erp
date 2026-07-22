<x-layouts.admin title="Chart of Accounts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Accounting', 'url' => '#'],
                ['label' => 'Chart of Accounts']
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your general ledger accounts and hierarchical structure.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <x-button type="primary" href="{{ route('admin.finance.accounting.chart-of-accounts.create') }}">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Account
            </x-button>
        </div>
    </div>

    {{-- Data Table --}}
    <x-card class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account Code</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50 transition-colors {{ $account->parent_id ? 'bg-gray-50/30' : '' }}">
                            <td class="py-4 px-4 text-sm font-medium {{ $account->parent_id ? 'text-gray-600 pl-8' : 'text-blue-600' }}">
                                {{ $account->code }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                                {{ $account->name }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ $account->accountType->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ $account->accountType->category ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-center">
                                @if($account->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-sm text-right flex justify-end space-x-2">
                                <a href="{{ route('admin.finance.accounting.chart-of-accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if(!$account->is_system)
                                    <form action="{{ route('admin.finance.accounting.chart-of-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new account.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
