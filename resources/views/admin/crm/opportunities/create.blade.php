<x-layouts.admin title="Create Opportunity">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    @can('create', App\Models\Opportunity::class)
    <div class="mb-8">
        <a href="{{ route('admin.crm.opportunities.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Opportunities
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Opportunity</h1>
        <p class="mt-1 text-sm text-gray-500 font-medium">Log a new deal and start tracking its progress through the pipeline.</p>
    </div>

    <form method="POST" action="{{ route('admin.crm.opportunities.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden mb-6" x-data="{ 
            pipelines: {{ Js::from($pipelines->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'stages' => $p->stages->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])) }},
            selectedPipeline: '{{ old('pipeline_id') }}',
            availableStages: [],
            updateStages() {
                let pipeline = this.pipelines.find(p => p.id == this.selectedPipeline);
                this.availableStages = pipeline ? pipeline.stages : [];
            },
            init() {
                if(!this.selectedPipeline && this.pipelines.length > 0) {
                    this.selectedPipeline = this.pipelines[0].id;
                }
                this.updateStages();
            }
        }">
            <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Deal Information</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                    @if(is_null(auth()->user()->company_id))
                        <div class="sm:col-span-2">
                            <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <x-input name="name" label="Opportunity Name" required />
                    </div>
                    
                    <div>
                        <x-input name="expected_revenue" label="Expected Revenue (RWF)" type="number" step="0.01" />
                    </div>
                    <div>
                        <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" value="50" />
                    </div>
                    
                    <div class="sm:col-span-2 border-t border-gray-100 my-2 pt-6">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 tracking-tight">Pipeline Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pipeline</label>
                                <select name="pipeline_id" x-model="selectedPipeline" @change="updateStages" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm text-gray-700">
                                    <option value="">Select a Pipeline</option>
                                    <template x-for="pipeline in pipelines" :key="pipeline.id">
                                        <option :value="pipeline.id" x-text="pipeline.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pipeline Stage</label>
                                <select name="pipeline_stage_id" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm text-gray-700">
                                    <template x-for="stage in availableStages" :key="stage.id">
                                        <option :value="stage.id" x-text="stage.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <x-select name="status" label="Status" :options="['Open' => 'Open', 'Won' => 'Won', 'Lost' => 'Lost']" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50/50 border-t border-gray-100 px-6 py-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.crm.opportunities.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none hover:shadow-md">Create Opportunity</button>
            </div>
        </div>
    </form>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to create opportunities.</p>
        <div class="mt-6">
            <a href="{{ route('admin.crm.opportunities.index') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">Return to Opportunities</a>
        </div>
    </div>
    @endcan
</x-layouts.admin>
