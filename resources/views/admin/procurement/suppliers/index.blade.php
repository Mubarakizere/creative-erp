<x-layouts.admin title="Suppliers Directory">
    <x-slot:breadcrumbs>
        @php
            $breadcrumbs = [
                ['label' => 'Procurement', 'url' => route('admin.procurement.requisitions.index')],
                ['label' => 'Suppliers'],
            ];
        @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Supplier::class)
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Suppliers Directory</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">Manage vendor profiles, contact details, categories, and preferred vendor status.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('create', App\Models\Supplier::class)
                    <a href="{{ route('admin.procurement.suppliers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xs transition-all hover:shadow-md shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add New Supplier
                    </a>
                @endcan
            </div>
        </div>

        {{-- KPI Statistics Banner --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Suppliers</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? $suppliers->total() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Registered vendor profiles</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Preferred Partners</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['preferred'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Vetted & priority suppliers</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Standard Vendors</div>
                    <div class="text-2xl font-black text-slate-700 mt-1">{{ $stats['standard'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Active supply partners</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Categories</div>
                    <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['categories'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Supplier classifications</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M19 7h.01M19 11h.01M19 15h.01"/></svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Preferred Status Filter Pills --}}
            <div class="flex items-center gap-1 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                @php
                    $prefFilter = request('preferred');
                @endphp
                <a href="{{ route('admin.procurement.suppliers.index', array_filter(['search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $prefFilter === null || $prefFilter === '' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Vendors
                </a>
                <a href="{{ route('admin.procurement.suppliers.index', array_filter(['preferred' => '1', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $prefFilter === '1' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Preferred Only
                </a>
                <a href="{{ route('admin.procurement.suppliers.index', array_filter(['preferred' => '0', 'search' => request('search')])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $prefFilter === '0' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Standard Only
                </a>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.procurement.suppliers.index') }}" class="w-full md:w-80 flex items-center gap-2">
                @if(request('preferred') !== null)
                    <input type="hidden" name="preferred" value="{{ request('preferred') }}">
                @endif
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" placeholder="Search name, code, phone, email..." value="{{ request('search') }}" class="block w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-xs">
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.procurement.suppliers.index', array_filter(['preferred' => request('preferred')])) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- Main Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6">Supplier Code</th>
                            <th class="py-3.5 px-6">Supplier Name</th>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Contact Info</th>
                            <th class="py-3.5 px-6 text-center">Status / Tier</th>
                            <th class="py-3.5 px-6 text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($suppliers as $supplier)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-4 px-6 font-extrabold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <span>{{ $supplier->code }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-slate-900 text-sm">{{ $supplier->name }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-600">
                                    {{ $supplier->category?->name ?? 'General Category' }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        @if($supplier->email)
                                            <div class="text-slate-700 font-medium flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span>{{ $supplier->email }}</span>
                                            </div>
                                        @endif
                                        @if($supplier->phone)
                                            <div class="text-slate-500 text-[11px] flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span>{{ $supplier->phone }}</span>
                                            </div>
                                        @endif
                                        @if(!$supplier->email && !$supplier->phone)
                                            <span class="text-slate-400 italic">No contact details</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($supplier->is_preferred)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Preferred Partner
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Standard Vendor
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <x-action-dropdown>
                                        @can('update', $supplier)
                                            <x-action-dropdown-item href="{{ route('admin.procurement.suppliers.edit', $supplier) }}" icon="edit">
                                                Edit Profile
                                            </x-action-dropdown-item>
                                        @endcan

                                        @can('delete', $supplier)
                                            <form method="POST" action="{{ route('admin.procurement.suppliers.destroy', $supplier) }}" id="delete-supplier-form-{{ $supplier->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <x-action-dropdown-item onclick="if(confirm('Are you sure you want to delete this supplier profile?')) document.getElementById('delete-supplier-form-{{ $supplier->id }}').submit()" icon="delete" variant="danger">
                                                Delete Supplier
                                            </x-action-dropdown-item>
                                        @endcan
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 px-6 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">No suppliers found</h3>
                                    <p class="text-slate-500 text-xs max-w-sm mx-auto mb-5">There are currently no suppliers matching your search or preferred vendor filter.</p>
                                    @can('create', App\Models\Supplier::class)
                                        <a href="{{ route('admin.procurement.suppliers.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-xs">
                                            Add New Supplier
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($suppliers, 'hasPages') && $suppliers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $suppliers->links('components.pagination') }}
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
        <p class="text-sm text-slate-500 font-medium">You do not have permission to view suppliers.</p>
    </div>
    @endcan
</x-layouts.admin>