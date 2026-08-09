<x-layouts.admin title="Create Pipeline">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Pipeline::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Create Pipeline</h1>
            <p class="mt-2 text-sm text-gray-500 font-medium">Design a new custom workflow for your sales processes.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.crm.pipelines.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:border-gray-300 rounded-xl px-4 py-2 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancel
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.crm.pipelines.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Info & Settings --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-3xl border border-blue-100/50 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 text-blue-200/40 opacity-50 pointer-events-none">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pipeline Settings</h3>
                        <p class="text-sm text-gray-600 font-medium mb-6 leading-relaxed">Configure the core behaviors of this pipeline. Default pipelines are automatically selected for new deals.</p>
                        
                        <div class="space-y-5">
                            {{-- Modern Toggle: Set as Default --}}
                            <label class="flex items-center justify-between cursor-pointer group bg-white/60 hover:bg-white p-3 rounded-2xl transition-all border border-transparent hover:border-white shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">Default Pipeline</span>
                                    <span class="text-xs font-medium text-gray-500">Use for new leads</span>
                                </div>
                                <div class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-gray-200 group-hover:bg-gray-300">
                                    <input type="checkbox" name="is_default" value="1" class="peer sr-only">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-colors"></div>
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                </div>
                            </label>

                            {{-- Modern Toggle: Active --}}
                            <label class="flex items-center justify-between cursor-pointer group bg-white/60 hover:bg-white p-3 rounded-2xl transition-all border border-transparent hover:border-white shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">Active Status</span>
                                    <span class="text-xs font-medium text-gray-500">Pipeline is available</span>
                                </div>
                                <div class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 bg-gray-200">
                                    <input type="checkbox" name="is_active" value="1" checked class="peer sr-only">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors"></div>
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-8 py-5">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Pipeline Details</h3>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        @if(is_null(auth()->user()->company_id))
                            <div class="max-w-md">
                                <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                            </div>
                        @endif
                        
                        <div>
                            <x-input name="name" label="Pipeline Name" placeholder="e.g., Enterprise Sales Pipeline" required />
                            <p class="mt-2 text-xs text-gray-500 font-medium">Use a descriptive name so your team can easily identify this process.</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/80 border-t border-gray-100 px-8 py-5 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:-translate-y-0.5">
                            Create Pipeline
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-50 mb-6 border border-red-100">
            <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-extrabold text-gray-900 mb-3">Access Denied</h3>
        <p class="text-base text-gray-500 font-medium">You do not have permission to create pipelines.</p>
        <div class="mt-8">
            <a href="{{ route('admin.crm.pipelines.index') }}" class="px-6 py-3 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Pipelines</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
