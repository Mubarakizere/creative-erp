<x-layouts.admin title="Asset Disposals">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Disposals'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asset Disposals & Sales</h1>
                <p class="mt-1 text-sm text-gray-500">Track asset write-offs, sales, and retirement requests.</p>
            </div>
            @can('create', App\Models\AssetDisposal::class)
                <x-button type="primary" @click="$dispatch('open-modal', 'create-disposal')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Request Disposal
                </x-button>
            @endcan
        </div>
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Asset</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Sale Price</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Reason</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
        </x-slot:head>

        @forelse($disposals as $disp)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $disp->asset->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $disp->asset->asset_code ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $disp->date ? \Carbon\Carbon::parse($disp->date)->format('M d, Y') : $disp->created_at->format('M d, Y') }}
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                    {{ $disp->type }}
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-600">
                    {{ $disp->sale_price ? number_format($disp->sale_price, 2) : '—' }}
                </td>
                <td class="px-4 py-3 hidden lg:table-cell text-sm text-gray-500">
                    {{ Str::limit($disp->reason, 40) }}
                </td>
                <td class="px-4 py-3">
                    <x-badge :type="match($disp->status) { 'Approved' => 'success', 'Pending Approval' => 'warning', 'Rejected' => 'danger', default => 'default' }">
                        {{ $disp->status }}
                    </x-badge>
                </td>
                <td class="px-4 py-3 text-right">
                    @if(($disp->status) === 'Pending Approval')
                        @can('approve', $disp)
                            <form method="POST" action="{{ route('admin.asset-disposals.approve', $disp) }}" class="inline">
                                @csrf
                                <x-button type="secondary" size="sm" submit>Approve & Post</x-button>
                            </form>
                        @endcan
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                    No disposal records found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $disposals->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    {{-- Request Disposal Modal --}}
    @can('create', App\Models\AssetDisposal::class)
    <x-modal id="create-disposal" maxWidth="lg">
        <x-slot:header>Request Asset Disposal / Sale</x-slot:header>
        <form method="POST" action="{{ route('admin.asset-disposals.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                    <select name="asset_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select equipment...</option>
                        @foreach(\App\Models\Asset::where('company_id', auth()->user()->company_id)->whereIn('status', ['Active', 'Fully Depreciated'])->orderBy('name')->get() as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Disposal Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="Disposal">Disposal / Scrap</option>
                            <option value="Sale">Sale</option>
                            <option value="Write-Off">Write-Off</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price (if applicable)</label>
                        <input type="number" name="sale_price" step="0.01" min="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Disposal Costs</label>
                        <input type="number" name="disposal_costs" step="0.01" min="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Disposal</label>
                    <textarea name="reason" rows="3" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Provide justification for disposing or selling this asset..."></textarea>
                </div>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Submit Request</x-button>
            </x-slot:footer>
        </form>
    </x-modal>
    @endcan
</x-layouts.admin>
