<x-layouts.admin title="Pipelines">
    @can('viewAny', App\Models\Pipeline::class)
    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pipelines</h1>
            <p class="mt-2 text-sm text-gray-500 font-medium">Manage and optimize your sales and support workflows.</p>
        </div>
        
        <div class="flex items-center gap-4">
            @can('create', App\Models\Pipeline::class)
            <a href="{{ route('admin.crm.pipelines.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Pipeline
            </a>
            @endcan
        </div>
    </div>

    {{-- Glassmorphism Filters --}}
    <div class="bg-white/70 backdrop-blur-md p-5 rounded-3xl border border-white/50 shadow-sm mb-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 z-0 pointer-events-none"></div>
        <form method="GET" action="{{ route('admin.crm.pipelines.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 relative z-10">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" placeholder="Search pipelines..." value="{{ request('search') }}" class="block w-full pl-11 pr-4 py-3 border-0 ring-1 ring-inset ring-gray-200 rounded-2xl leading-5 bg-white/80 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-all shadow-sm hover:bg-white hover:ring-gray-300">
            </div>
            <div class="flex items-center gap-3 justify-end">
                <button type="submit" class="inline-flex items-center px-5 py-3 text-sm font-semibold text-gray-700 bg-white rounded-2xl hover:bg-gray-50 transition-all shadow-sm border border-gray-200 hover:border-gray-300 hover:shadow-md">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('admin.crm.pipelines.index') }}" class="inline-flex items-center px-5 py-3 text-sm font-semibold text-gray-500 hover:text-red-600 bg-white/50 border border-transparent rounded-2xl transition-all hover:bg-red-50">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Premium Pipelines Data Grid --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Pipeline Details</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 hidden sm:table-cell">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Stages</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($pipelines as $pipeline)
                        <tr class="hover:bg-blue-50/40 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-100 border border-indigo-200/50 flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.crm.pipelines.show', $pipeline) }}" class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $pipeline->name }}
                                        </a>
                                        @if($pipeline->company)
                                            <p class="text-xs text-gray-500 mt-1 font-medium flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                {{ $pipeline->company->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-5 hidden sm:table-cell">
                                <div class="flex flex-col gap-2 items-start">
                                    @if($pipeline->is_active ?? true)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                    
                                    @if($pipeline->is_default ?? false)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/50 uppercase tracking-widest mt-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Default
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <span class="text-sm font-extrabold text-gray-900 mr-2">{{ $pipeline->stages_count ?? (method_exists($pipeline, 'stages') ? $pipeline->stages()->count() : 0) }}</span>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stages</span>
                                    <div class="ml-4 flex -space-x-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                        {{-- Simulated visual stages --}}
                                        <div class="w-6 h-2 rounded-l-full bg-blue-400 border-r border-white"></div>
                                        <div class="w-6 h-2 bg-indigo-400 border-r border-white"></div>
                                        <div class="w-6 h-2 rounded-r-full bg-emerald-400"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @can('view', $pipeline)
                                    <a href="{{ route('admin.crm.pipelines.show', $pipeline) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all shadow-sm hover:shadow" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @endcan
                                    
                                    @can('update', $pipeline)
                                    <a href="{{ route('admin.crm.pipelines.edit', $pipeline) }}" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all shadow-sm hover:shadow" title="Edit Settings">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @endcan
                                    
                                    @can('delete', $pipeline)
                                    <button type="button" @click="open = false; $dispatch('open-modal', 'delete-pipeline-{{ $pipeline->id }}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all shadow-sm hover:shadow" title="Delete Pipeline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Delete Modal --}}
                        @can('delete', $pipeline)
                            <x-modal id="delete-pipeline-{{ $pipeline->id }}" maxWidth="md">
                                <x-slot:header>Delete Pipeline</x-slot:header>
                                <div class="text-center py-6">
                                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-6 border border-red-100">
                                        <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <h3 class="text-xl font-extrabold text-gray-900 mb-2 tracking-tight">Delete Pipeline?</h3>
                                    <p class="text-sm text-gray-500 font-medium px-4">Are you sure you want to delete <strong>{{ $pipeline->name }}</strong>? This action cannot be undone.</p>
                                </div>
                                <x-slot:footer>
                                    <div class="flex items-center gap-3 w-full">
                                        <button type="button" @click="open = false" class="flex-1 px-4 py-3 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all">Cancel</button>
                                        <form method="POST" action="{{ route('admin.crm.pipelines.destroy', $pipeline) }}" class="flex-1 m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-4 py-3 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">Confirm Delete</button>
                                        </form>
                                    </div>
                                </x-slot:footer>
                            </x-modal>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-200/50 shadow-inner">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900 mb-2">No pipelines configured</h3>
                                <p class="text-base text-gray-500 font-medium max-w-sm mx-auto">Get started by creating your first sales or support pipeline to track your workflows.</p>
                                @can('create', App\Models\Pipeline::class)
                                    <a href="{{ route('admin.crm.pipelines.create') }}" class="mt-8 inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-md transition-all hover:-translate-y-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Create Pipeline
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($pipelines, 'hasPages') && $pipelines->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $pipelines->links('components.pagination') }}
        </div>
        @endif
    </div>
    @else
    <div class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-50 mb-6 border border-red-100">
            <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-extrabold text-gray-900 mb-3">Access Denied</h3>
        <p class="text-base text-gray-500 font-medium">You do not have permission to view pipelines.</p>
    </div>
    @endcan
</x-layouts.admin>
