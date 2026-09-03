<x-layouts.admin title="Equipment Transfers">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Transfers'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Equipment Transfers</h1>
                <p class="mt-1 text-sm text-gray-500">Track and manage equipment location and custody transfers.</p>
            </div>
            @can('create', App\Models\AssetTransfer::class)
                <x-button type="primary" @click="$dispatch('open-modal', 'create-transfer')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Initiate Transfer
                </x-button>
            @endcan
        </div>
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipment</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">From</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">To</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Transfer Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
        </x-slot:head>

        @forelse($transfers as $t)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $t->asset->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $t->asset->asset_code ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $t->fromUser->name ?? ($t->fromDepartment->name ?? '—') }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $t->toUser->name ?? ($t->toDepartment->name ?? '—') }}
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500">
                    {{ $t->transfer_date ? \Carbon\Carbon::parse($t->transfer_date)->format('M d, Y') : $t->created_at->format('M d, Y') }}
                </td>
                <td class="px-4 py-3">
                    <x-badge :type="match($t->status) { 'Approved' => 'success', 'Pending' => 'warning', 'Rejected' => 'danger', default => 'default' }">
                        {{ $t->status ?? 'Pending' }}
                    </x-badge>
                </td>
                <td class="px-4 py-3 text-right">
                    @if(($t->status ?? 'Pending') === 'Pending')
                        <x-action-dropdown>
                            @can('approve', $t)
                                <form method="POST" action="{{ route('admin.asset-transfers.approve', $t) }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approve Transfer
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.asset-transfers.reject', $t) }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject Transfer
                                    </button>
                                </form>
                            @endcan
                        </x-action-dropdown>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                    No transfer records found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $transfers->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    {{-- Create Transfer Modal --}}
    @can('create', App\Models\AssetTransfer::class)
    <x-modal id="create-transfer" maxWidth="lg">
        <x-slot:header>Initiate Equipment Transfer</x-slot:header>
        <form method="POST" action="{{ route('admin.asset-transfers.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                    <select name="asset_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select equipment...</option>
                        @foreach(\App\Models\Asset::where('company_id', auth()->user()->company_id)->orderBy('name')->get() as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Date</label>
                    <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to User</label>
                        <select name="to_user_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select user...</option>
                            @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Department</label>
                        <select name="to_department_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select department...</option>
                            @foreach(\App\Models\Department::where('company_id', auth()->user()->company_id)->orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Transfer</label>
                    <textarea name="reason" rows="3" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Explain the reason for this transfer..."></textarea>
                </div>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Initiate Transfer</x-button>
            </x-slot:footer>
        </form>
    </x-modal>
    @endcan
</x-layouts.admin>
