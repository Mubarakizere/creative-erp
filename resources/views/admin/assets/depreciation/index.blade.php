<x-layouts.admin title="Asset Depreciation">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Equipment', 'url' => route('admin.assets.index')],
                ['label' => 'Depreciation'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asset Depreciation Schedules</h1>
                <p class="mt-1 text-sm text-gray-500">Generate and post monthly asset depreciation records.</p>
            </div>
            @can('depreciate', App\Models\Asset::class)
                <x-button type="primary" @click="$dispatch('open-modal', 'generate-depreciation')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Generate Monthly Preview
                </x-button>
            @endcan
        </div>
    </div>

    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Asset</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Period</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Accumulated</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Book Value</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
        </x-slot:head>

        @forelse($depreciations as $d)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $d->asset->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $d->asset->asset_code ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $d->period_date ? \Carbon\Carbon::parse($d->period_date)->format('M Y') : '—' }}
                </td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                    {{ number_format($d->amount ?? 0, 2) }}
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500">
                    {{ number_format($d->accumulated_depreciation ?? 0, 2) }}
                </td>
                <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500">
                    {{ number_format($d->book_value ?? 0, 2) }}
                </td>
                <td class="px-4 py-3">
                    <x-badge :type="match($d->status) { 'Posted' => 'success', 'Preview' => 'warning', default => 'default' }">
                        {{ $d->status ?? 'Preview' }}
                    </x-badge>
                </td>
                <td class="px-4 py-3 text-right">
                    @if(($d->status ?? 'Preview') === 'Preview')
                        @can('depreciate', $d->asset)
                            <form method="POST" action="{{ route('admin.asset-depreciations.post', $d) }}" class="inline">
                                @csrf
                                <x-button type="secondary" size="sm" submit>Post to Journal</x-button>
                            </form>
                        @endcan
                    @else
                        <span class="text-xs text-gray-400">Posted</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                    No depreciation records found.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $depreciations->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>

    {{-- Generate Preview Modal --}}
    @can('depreciate', App\Models\Asset::class)
    <x-modal id="generate-depreciation" maxWidth="sm">
        <x-slot:header>Generate Monthly Depreciation</x-slot:header>
        <form method="POST" action="{{ route('admin.asset-depreciations.generate') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period End Date</label>
                    <input type="date" name="period_end" value="{{ date('Y-m-t') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <x-slot:footer>
                <x-button type="ghost" @click="open = false">Cancel</x-button>
                <x-button type="primary" submit>Generate Preview</x-button>
            </x-slot:footer>
        </form>
    </x-modal>
    @endcan
</x-layouts.admin>
