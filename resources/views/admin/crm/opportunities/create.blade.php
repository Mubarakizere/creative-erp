<x-layouts.admin title="Create Opportunity">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => 'Create']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Opportunity</h1>
        </div>
        <x-button type="ghost" href="{{ route('admin.crm.opportunities.index') }}" size="sm">Back to List</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.opportunities.store') }}">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{ 
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
                @if(is_null(auth()->user()->company_id))
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" required />
                @endif
                <x-input name="name" label="Opportunity Name" required />
                <x-input name="expected_revenue" label="Expected Revenue ($)" type="number" step="0.01" />
                <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" value="50" />
                
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pipeline</label>
                    <select name="pipeline_id" x-model="selectedPipeline" @change="updateStages" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select a Pipeline</option>
                        <template x-for="pipeline in pipelines" :key="pipeline.id">
                            <option :value="pipeline.id" x-text="pipeline.name"></option>
                        </template>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pipeline Stage</label>
                    <select name="pipeline_stage_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <template x-for="stage in availableStages" :key="stage.id">
                            <option :value="stage.id" x-text="stage.name"></option>
                        </template>
                    </select>
                </div>

                <x-select name="status" label="Status" :options="['Open' => 'Open', 'Won' => 'Won', 'Lost' => 'Lost']" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.opportunities.index') }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Create Opportunity</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
