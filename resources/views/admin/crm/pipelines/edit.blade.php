<x-layouts.admin title="Edit Pipeline">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => 'Edit']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Pipeline</h1>
            <p class="mt-2 text-sm text-gray-500 font-medium">Update the settings and name of this sales pipeline.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.crm.pipelines.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 hover:border-gray-300 rounded-xl px-4 py-2 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancel
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.crm.pipelines.update', $pipeline) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Info & Settings --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 rounded-3xl border border-amber-100/50 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 text-amber-200/40 opacity-50 pointer-events-none">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pipeline Settings</h3>
                        <p class="text-sm text-gray-600 font-medium mb-6 leading-relaxed">Modify the core configuration for this pipeline. Default pipelines are automatically selected for new deals.</p>
                        
                        <div class="space-y-5">
                            {{-- Modern Toggle: Set as Default --}}
                            <label class="flex items-center justify-between cursor-pointer group bg-white/60 hover:bg-white p-3 rounded-2xl transition-all border border-transparent hover:border-white shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">Default Pipeline</span>
                                    <span class="text-xs font-medium text-gray-500">Use for new leads</span>
                                </div>
                                <div class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 bg-gray-200 group-hover:bg-gray-300">
                                    <input type="checkbox" name="is_default" value="1" {{ $pipeline->is_default ? 'checked' : '' }} class="peer sr-only">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-amber-500 transition-colors"></div>
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
                                </div>
                            </label>

                            {{-- Modern Toggle: Active --}}
                            <label class="flex items-center justify-between cursor-pointer group bg-white/60 hover:bg-white p-3 rounded-2xl transition-all border border-transparent hover:border-white shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900">Active Status</span>
                                    <span class="text-xs font-medium text-gray-500">Pipeline is available</span>
                                </div>
                                <div class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 bg-gray-200 group-hover:bg-gray-300">
                                    <input type="checkbox" name="is_active" value="1" {{ $pipeline->is_active ? 'checked' : '' }} class="peer sr-only">
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
                    <div class="bg-gray-50/50 border-b border-gray-100 px-8 py-5 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Pipeline Details</h3>
                        @if($pipeline->created_at)
                            <span class="text-xs text-gray-400 font-medium">Created {{ $pipeline->created_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div>
                            <x-input name="name" label="Pipeline Name" :value="$pipeline->name" placeholder="e.g., Enterprise Sales Pipeline" required />
                            <p class="mt-2 text-xs text-gray-500 font-medium">Use a descriptive name so your team can easily identify this process.</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/80 border-t border-gray-100 px-8 py-5 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl hover:from-amber-600 hover:to-orange-600 shadow-sm transition-all focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 hover:-translate-y-0.5">
                            Update Pipeline
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
