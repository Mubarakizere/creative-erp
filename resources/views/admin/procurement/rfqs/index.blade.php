<x-layouts.admin title="Request for Quotations">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Request for Quotations'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\SupplierQuotation::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Request for Quotations</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage requests for quotations from suppliers.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', App\Models\SupplierQuotation::class)
                <a href="{{ route('admin.procurement.rfqs.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New RFQ
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.procurement.rfqs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search RFQs..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm min-h-[42px]">
            </div>
            
            <div class="flex items-end gap-2 lg:col-span-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200 w-full justify-center sm:w-auto min-h-[42px]">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('admin.procurement.rfqs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors shrink-0 min-h-[42px]">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Number</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Supplier</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Issue Date</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden lg:table-cell">Valid Until</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($rfqs as $rfq)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900">{{ $rfq->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">{{ $rfq->supplier?->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="text-sm text-gray-600">{{ $rfq->issue_date }}</span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-sm text-gray-600">{{ $rfq->valid_until }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-action-dropdown>
                                @can('view', $rfq)
                                    <x-action-dropdown-item href="{{ route('admin.procurement.rfqs.show', $rfq) }}" icon="view">
                                        View Details
                                    </x-action-dropdown-item>
                                @endcan

                                @can('update', $rfq)
                                    <x-action-dropdown-item href="{{ route('admin.procurement.rfqs.edit', $rfq) }}" icon="edit">
                                        Edit RFQ
                                    </x-action-dropdown-item>
                                @endcan

                                @can('delete', $rfq)
                                    <form method="POST" action="{{ route('admin.procurement.rfqs.destroy', $rfq) }}" id="delete-rfq-form-{{ $rfq->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <x-action-dropdown-item onclick="document.getElementById('delete-rfq-form-{{ $rfq->id }}').submit()" icon="delete" variant="danger">
                                        Delete RFQ
                                    </x-action-dropdown-item>
                                @endcan
                            </x-action-dropdown>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">No RFQs found</h3>
                            <p class="text-sm text-gray-500 font-medium">Create your first Request for Quotation to get started.</p>
                            @can('create', App\Models\SupplierQuotation::class)
                                <a href="{{ route('admin.procurement.rfqs.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    New RFQ
                                </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($rfqs, 'hasPages') && $rfqs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $rfqs->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view Request for Quotations.</p>
    </div>
    @endcan
</x-layouts.admin>