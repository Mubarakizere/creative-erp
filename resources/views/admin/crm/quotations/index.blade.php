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

    @can('viewAny', App\Models\Quotation::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Quotations</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage sales quotations and prepare invoices.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\Quotation::class)
                <a href="{{ route('admin.crm.quotations.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create Quotation
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.crm.quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="relative sm:col-span-3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search reference, customer..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </div>
            <div class="flex items-center gap-2 justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200 w-full justify-center sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.crm.quotations.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors shrink-0">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Reference</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Customer</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Date / Valid Until</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Total</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

        @forelse($quotations as $quotation)
                        <tr class="hover:bg-blue-50/30 transition-colors group {{ $quotation->trashed() ? 'bg-red-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $quotation->quotation_number }}
                                    </a>
                                    @if($quotation->reference)
                                        <p class="text-xs text-gray-500 mt-0.5 font-medium">Ref: {{ $quotation->reference }}</p>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 hidden sm:table-cell text-sm text-gray-600 font-medium">
                                @if($quotation->account)
                                    {{ $quotation->account->name }}
                                @elseif($quotation->lead)
                                    {{ $quotation->lead->first_name }} {{ $quotation->lead->last_name }}
                                @elseif($quotation->opportunity)
                                    {{ $quotation->opportunity->name }}
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm text-gray-600 font-medium">{{ $quotation->created_at->format('M d, Y') }}</div>
                                @if($quotation->valid_until)
                                    <div class="text-xs text-gray-500 mt-0.5">Valid: {{ \Carbon\Carbon::parse($quotation->valid_until)->format('M d, Y') }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-sm font-medium text-gray-700">RWF {{ number_format($quotation->grand_total, 2) }}</span>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $statusType = match($quotation->status?->name) {
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
                                <x-badge :type="$statusType">{{ $quotation->status?->name ?? 'Draft' }}</x-badge>
                                @if($quotation->trashed())
                                    <x-badge type="danger" class="ml-1">Archived</x-badge>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                    
<div class="flex items-center justify-end gap-2">


                            @can('view', $quotation)
                                <a href="{{ route('admin.crm.quotations.show', $quotation) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="View">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
</a>
                            @endcan

                            @if(!$quotation->trashed())
                                @can('update', $quotation)
                                    <a href="{{ route('admin.crm.quotations.edit', $quotation) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors flex items-center justify-center" title="Edit">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
</a>
                                @endcan
                                
                                @can('export', $quotation)
                                    <form method="POST" action="{{ route('admin.crm.quotations.export', $quotation) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Export PDF">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
</button>
                                    </form>
                                @endcan

                                @if($quotation->status?->name === 'Pending Approval')
                                    @can('approve', $quotation)
                                        
                                        <form method="POST" action="{{ route('admin.crm.quotations.approve', $quotation) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors flex items-center justify-center" title="Approve">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.crm.quotations.reject', $quotation) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center" title="Reject">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
</button>
                                        </form>
                                    @endcan
                                @endif

                                @can('create', App\Models\Invoice::class)
                                    @if($quotation->status?->name === 'Approved')
                                        <a href="{{ route('admin.finance.invoices.create', ['quotation_id' => $quotation->id]) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Generate Invoice">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
</a>
                                    @endif
                                @endcan

                                @can('create', App\Models\Quotation::class)
                                    <form method="POST" action="{{ route('admin.crm.quotations.duplicate', $quotation) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Duplicate">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-4a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
</button>
                                    </form>
                                @endcan

                                @can('delete', $quotation)
                                    
                                    <button @click="open = false; $dispatch('open-modal', 'archive-quotation-{{ $quotation->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Archive">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
</button>
                                    <button @click="open = false; $dispatch('open-modal', 'delete-quotation-{{ $quotation->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center" title="Delete">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
</button>
                                @endcan
                            @endif
                        
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

                <x-modal id="delete-quotation-{{ $quotation->id }}" maxWidth="md">
                    <x-slot:header>Delete Quotation</x-slot:header>

                    <div class="text-center py-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Quotation {{ $quotation->quotation_number }}?</h3>
                        <p class="text-sm text-gray-500">This action will permanently delete the quotation. This cannot be undone.</p>
                    </div>

                    <x-slot:footer>
                        <x-button type="ghost" @click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('admin.crm.quotations.destroy', ['quotation' => $quotation->id, 'force' => 1]) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-button type="danger" submit>Delete Quotation</x-button>
                        </form>
                    </x-slot:footer>
                </x-modal>
            @endif
        @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No quotations found</h3>
                    <p class="text-sm text-gray-500 font-medium">Create your first quotation to start preparing sales documents.</p>
                    @can('create', App\Models\Quotation::class)
                        <a href="{{ route('admin.crm.quotations.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            Create Quotation
                        </a>
                    @endcan
                </td>
            </tr>
        @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($quotations, 'hasPages') && $quotations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $quotations->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view quotations.</p>
    </div>
    @endcan
</x-layouts.admin>
