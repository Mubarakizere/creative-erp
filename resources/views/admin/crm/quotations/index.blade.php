<x-layouts.admin title="Quotations">
    {{-- Breadcrumbs --}}
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'CRM', 'url' => '#'],
                ['label' => 'Quotations'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quotations</h1>
                <p class="mt-1 text-sm text-gray-500">Manage sales quotations and prepare invoices.</p>
            </div>
            @can('create', App\Models\Quotation::class)
                <x-button type="primary" href="{{ route('admin.crm.quotations.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Quotation
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.crm.quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <x-input
                name="search"
                placeholder="Search reference, customer..."
                :value="request('search')"
                :icon="'<svg class=&quot;w-4 h-4&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z&quot;/></svg>'"
            />
            
            {{-- Actions --}}
            <div class="flex items-end gap-2 lg:col-span-3">
                <x-button type="primary" size="md">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'status']))
                    <x-button type="ghost" href="{{ route('admin.crm.quotations.index') }}" size="md">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Quotations Table --}}
    <x-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Date / Valid Until</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Total</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Actions</th>
        </x-slot:head>

        @forelse($quotations as $quotation)
            <tr @class(['bg-red-50/30' => $quotation->trashed()])>
                {{-- Reference --}}
                <td class="px-4 py-3">
                    <div>
                        <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ $quotation->quotation_number }}
                        </a>
                        @if($quotation->reference)
                            <p class="text-xs text-gray-500 mt-0.5">Ref: {{ $quotation->reference }}</p>
                        @endif
                    </div>
                </td>

                {{-- Customer Info --}}
                <td class="px-4 py-3 hidden sm:table-cell">
                    <div class="text-sm text-gray-900 font-medium">
                        @if($quotation->account)
                            {{ $quotation->account->name }}
                        @elseif($quotation->lead)
                            {{ $quotation->lead->first_name }} {{ $quotation->lead->last_name }}
                        @elseif($quotation->opportunity)
                            {{ $quotation->opportunity->name }}
                        @else
                            <span class="text-gray-400">Not specified</span>
                        @endif
                    </div>
                </td>

                {{-- Dates --}}
                <td class="px-4 py-3 hidden md:table-cell">
                    <div class="text-sm text-gray-600">{{ $quotation->created_at->format('M d, Y') }}</div>
                    @if($quotation->valid_until)
                        <div class="text-xs text-gray-500 mt-0.5">Valid: {{ \Carbon\Carbon::parse($quotation->valid_until)->format('M d, Y') }}</div>
                    @endif
                </td>

                {{-- Value --}}
                <td class="px-4 py-3 hidden md:table-cell">
                    <span class="text-sm font-medium text-gray-700">${{ number_format($quotation->grand_total, 2) }}</span>
                </td>

                {{-- Status --}}
                <td class="px-4 py-3">
                    @php
                        $statusType = match($quotation->status) {
                            'Draft' => 'default',
                            'Pending Approval' => 'warning',
                            'Approved' => 'success',
                            'Rejected' => 'danger',
                            'Sent' => 'info',
                            'Accepted' => 'success',
                            'Declined' => 'danger',
                            'Expired' => 'default',
                            'Converted' => 'primary',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ $quotation->status ?? 'Draft' }}</x-badge>
                    @if($quotation->trashed())
                        <x-badge type="danger" class="ml-1">Archived</x-badge>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3 text-right">
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1"
                             style="display: none;">

                            @can('view', $quotation)
                                <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            @endcan

                            @if(!$quotation->trashed())
                                @can('update', $quotation)
                                    <a href="{{ route('admin.crm.quotations.edit', $quotation) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @endcan
                                
                                @can('export', $quotation)
                                    <form method="POST" action="{{ route('admin.crm.quotations.export', $quotation) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Export PDF
                                        </button>
                                    </form>
                                @endcan

                                @can('create', App\Models\Quotation::class)
                                    <form method="POST" action="{{ route('admin.crm.quotations.duplicate', $quotation) }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicate
                                        </button>
                                    </form>
                                @endcan

                                @can('delete', $quotation)
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <button @click="open = false; $dispatch('open-modal', 'archive-quotation-{{ $quotation->id }}')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Archive
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </td>
            </tr>

            {{-- Archive Modal --}}
            @if(!$quotation->trashed())
                <x-modal id="archive-quotation-{{ $quotation->id }}" maxWidth="md">
                    <x-slot:header>Archive Quotation</x-slot:header>

                    <div class="text-center py-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Quotation {{ $quotation->quotation_number }}?</h3>
                        <p class="text-sm text-gray-500">This action will soft-delete the quotation. You can restore it later.</p>
                    </div>

                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.crm.quotations.destroy', $quotation) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Archive Quotation</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endif
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">No quotations found</p>
                        <p class="text-gray-400 text-xs mt-1">Create your first quotation to start preparing sales documents.</p>
                        @can('create', App\Models\Quotation::class)
                            <x-button type="primary" href="{{ route('admin.crm.quotations.create') }}" class="mt-4" size="sm">
                                Create Quotation
                            </x-button>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $quotations->links('components.pagination') }}
        </x-slot:pagination>
    </x-table>
</x-layouts.admin>
