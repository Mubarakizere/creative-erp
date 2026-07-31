<x-layouts.admin title="Create Pipeline">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Pipelines', 'url' => route('admin.crm.pipelines.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Pipeline::class)
    <div class="mb-8">
        <a href="{{ route('admin.crm.pipelines.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Pipelines
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Pipeline</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Define a new sales process with custom stages.</p>
    </div>

    <form method="POST" action="{{ route('admin.crm.pipelines.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Pipeline Details</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                    @if(is_null(auth()->user()->company_id))
                        <div class="sm:col-span-2">
                            <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <x-input name="name" label="Pipeline Name" required />
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row gap-6 pt-6 border-t border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="is_default" value="1" class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 transition-all checked:border-blue-600 checked:bg-blue-600 hover:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor" stroke-width="1">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 group-hover:text-gray-900 transition-colors">Set as Default Pipeline</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 transition-all checked:border-emerald-500 checked:bg-emerald-500 hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 transition-opacity peer-checked:opacity-100">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor" stroke-width="1">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 group-hover:text-gray-900 transition-colors">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.crm.pipelines.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Create Pipeline</button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create pipelines.</p>
        <div class="mt-6">
            <a href="{{ route('admin.crm.pipelines.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Pipelines</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
