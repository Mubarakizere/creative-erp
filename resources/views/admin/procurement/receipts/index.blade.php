<x-layouts.admin title="Goods Receipts">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.pos.index')],
                ['label' => 'Goods Receipts'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\GoodsReceipt::class)
    <div class="space-y-6">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Goods Receipts (GRN)</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Manage and view incoming warehouse deliveries posted against purchase orders.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\GoodsReceipt::class)
                    <a href="{{ route('admin.procurement.pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Receive Goods (Select PO)
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filter Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.procurement.receipts.index') }}" class="w-full md:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search GRN code, PO code..." value="{{ request('search') }}" class="block w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                </div>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-200 transition-colors shadow-xs shrink-0">
                    Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.procurement.receipts.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">GRN Code</th>
                            <th class="py-3.5 px-6">Purchase Order</th>
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Receipt Date</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($receipts as $gr)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-4 px-6 font-extrabold text-slate-900">
                                    <a href="{{ route('admin.procurement.receipts.show', $gr->id) }}" class="hover:text-indigo-600 transition-colors flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <span>{{ $gr->code }}</span>
                                    </a>
                                </td>
                                <td class="py-4 px-6 font-bold">
                                    @if($gr->purchaseOrder)
                                        <a href="{{ route('admin.procurement.pos.show', $gr->purchaseOrder->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                            {{ $gr->purchaseOrder->code }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-700">
                                    {{ $gr->purchaseOrder?->supplier?->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-600">
                                    {{ $gr->receipt_date ? \Carbon\Carbon::parse($gr->receipt_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 capitalize">
                                        {{ $gr->status ?? 'Completed' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <x-action-dropdown>
                                        @can('view', $gr)
                                            <x-action-dropdown-item href="{{ route('admin.procurement.receipts.show', $gr->id) }}" icon="view">
                                                View Details
                                            </x-action-dropdown-item>
                                        @endcan

                                        @can('delete', $gr)
                                            <form method="POST" action="{{ route('admin.procurement.receipts.destroy', $gr->id) }}" id="delete-gr-form-{{ $gr->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this Goods Receipt?')) document.getElementById('delete-gr-form-{{ $gr->id }}').submit()" icon="delete" variant="danger">
                                                Delete Receipt
                                            </x-action-dropdown-item>
                                        @endcan
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 px-6 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">No goods receipts found</h3>
                                    <p class="text-slate-500 text-xs max-w-sm mx-auto mb-5">Receive goods against approved purchase orders to post incoming inventory.</p>
                                    @can('create', App\Models\GoodsReceipt::class)
                                        <a href="{{ route('admin.procurement.pos.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                                            View Purchase Orders
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($receipts, 'hasPages') && $receipts->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $receipts->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4 border border-rose-200">
            <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Access Denied</h3>
        <p class="text-sm text-slate-500 font-medium">You do not have permission to view goods receipts.</p>
    </div>
    @endcan
</x-layouts.admin>