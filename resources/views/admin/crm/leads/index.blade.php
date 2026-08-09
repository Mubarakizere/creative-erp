<x-layouts.admin title="Leads">
    @can('viewAny', App\Models\Lead::class)
    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Leads</h1>
            <p class="mt-1 text-sm text-gray-500 font-medium">Manage your incoming leads and prospects.</p>
        </div>
        
        <div class="flex items-center gap-4">
            @can('create', App\Models\Lead::class)
            <a href="{{ route('admin.crm.leads.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Lead
            </a>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-200/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.crm.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search name, company, email..." value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm">
            </div>
            
            {{-- Status Filter --}}
            <div>
                <select name="status" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm text-gray-700">
                    <option value="">All Statuses</option>
                    <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Contacted" {{ request('status') == 'Contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="Qualified" {{ request('status') == 'Qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="Lost" {{ request('status') == 'Lost' ? 'selected' : '' }}>Lost</option>
                    <option value="Converted" {{ request('status') == 'Converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 lg:col-span-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors shadow-sm border border-gray-200">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'source_id', 'industry_id', 'owner_id']))
                    <a href="{{ route('admin.crm.leads.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-900 bg-white border border-transparent hover:border-gray-300 rounded-xl transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Leads Table --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/60">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Name & Company</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Contact</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 hidden md:table-cell">Value</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-blue-50/30 transition-colors group {{ $lead->trashed() ? 'opacity-60 bg-gray-50/50' : '' }}">
                        <td class="px-6 py-4">
                            <div>
                                <a href="{{ route('admin.crm.leads.show', $lead) }}" class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    {{ $lead->first_name }} {{ $lead->last_name }}
                                </a>
                                @if($lead->company_name)
                                    <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $lead->company_name }}</p>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div class="text-sm font-medium text-gray-600">{{ $lead->email }}</div>
                            @if($lead->phone)
                                <div class="text-xs text-gray-500 mt-0.5">{{ $lead->phone }}</div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 hidden md:table-cell">
                            @if($lead->expected_value)
                                <span class="text-sm font-bold text-gray-700">{{ number_format($lead->expected_value, 2) }} RWF</span>
                            @else
                                <span class="text-xs font-medium text-gray-400">Not specified</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider
                                @if($lead->status === 'New') bg-blue-100 text-blue-800
                                @elseif($lead->status === 'Contacted') bg-purple-100 text-purple-800
                                @elseif($lead->status === 'Qualified') bg-amber-100 text-amber-800
                                @elseif($lead->status === 'Converted') bg-emerald-100 text-emerald-800
                                @elseif($lead->status === 'Lost') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif
                            ">
                                {{ $lead->status }}
                            </span>
                            @if($lead->trashed())
                                <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-gray-200 text-gray-600">Archived</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            
<div class="flex items-center justify-end gap-2">

                                     
                                    @can('view', $lead)
                                    <a href="{{ route('admin.crm.leads.show', $lead) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="View">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</a>
                                    @endcan
                                    
                                    @if(!$lead->trashed())
                                        @can('update', $lead)
                                        <a href="{{ route('admin.crm.leads.edit', $lead) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors flex items-center justify-center" title="Edit">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</a>
                                        @endcan
                                        
                                        @if(in_array($lead->status, ['New', 'Contacted', 'Qualified']))
                                            
                                            @can('convert', $lead)
                                                <form method="POST" action="{{ route('admin.crm.leads.convert', $lead) }}">
                                                    @csrf
                                                    <input type="hidden" name="create_account" value="1">
                                                    <input type="hidden" name="create_contact" value="1">
                                                    <input type="hidden" name="create_opportunity" value="1">
                                                    <input type="hidden" name="opportunity_name" value="{{ $lead->company_name ?? $lead->last_name . ' Deal' }}">
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Convert Lead">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
</button>
                                                </form>
                                            @endcan
                                        @endif
                                        
                                        @can('delete', $lead)
                                            
                                            <button type="button" @click="open = false; $dispatch('open-modal', 'archive-lead-{{ $lead->id }}')" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Archive">
<svg class="w-4 h-4 mr-3 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
</button>
                                        @endcan
                                    @endif
                                
</div>
</td>
                    </tr>
                    
                    {{-- Archive Modal --}}
                    @if(!$lead->trashed())
                        <x-modal id="archive-lead-{{ $lead->id }}" maxWidth="md">
                            <x-slot:header>Archive Lead</x-slot:header>
                            <div class="text-center py-4">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4 border border-red-200">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 tracking-tight">Archive Lead?</h3>
                                <p class="text-sm text-gray-500 font-medium">Are you sure you want to archive <strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong>? You can restore it later.</p>
                            </div>
                            <x-slot:footer>
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                                <form method="POST" action="{{ route('admin.crm.leads.destroy', $lead) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Archive</button>
                                </form>
                            </x-slot:footer>
                        </x-modal>
                    @endif
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">No leads found</h3>
                            <p class="text-sm text-gray-500 font-medium">Create your first lead to start tracking prospects.</p>
                            @can('create', App\Models\Lead::class)
                                <a href="{{ route('admin.crm.leads.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    Create Lead
                                </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $leads->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view leads.</p>
    </div>
    @endcan
</x-layouts.admin>
