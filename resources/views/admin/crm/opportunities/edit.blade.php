<x-layouts.admin title="Edit Opportunity">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => 'Edit']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Edit Opportunity</h1></div>
        <x-button type="ghost" href="{{ route('admin.crm.opportunities.show', $opportunity) }}" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('admin.crm.opportunities.update', $opportunity) }}">
        @csrf
        @method('PUT')
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{ 
                pipelines: {{ Js::from($pipelines->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'stages' => $p->stages->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])) }},
                selectedPipeline: '{{ old('pipeline_id', $opportunity->pipeline_id) }}',
                selectedStage: '{{ old('pipeline_stage_id', $opportunity->pipeline_stage_id) }}',
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
                    <x-select name="company_id" label="Company Context" :options="$companies->pluck('name', 'id')->toArray()" :selected="$opportunity->company_id" required />
                @endif
                <x-input name="name" label="Opportunity Name" :value="$opportunity->name" required />
                <x-input name="expected_revenue" label="Expected Revenue ($)" type="number" step="0.01" :value="$opportunity->expected_revenue" />
                <x-input name="probability" label="Probability (%)" type="number" min="0" max="100" :value="$opportunity->probability" />
                
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
                    <select name="pipeline_stage_id" x-model="selectedStage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <template x-for="stage in availableStages" :key="stage.id">
                            <option :value="stage.id" x-text="stage.name"></option>
                        </template>
                    </select>
                </div>

                <x-select name="status" label="Status" :options="['Open' => 'Open', 'Won' => 'Won', 'Lost' => 'Lost']" :selected="$opportunity->status" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="ghost" href="{{ route('admin.crm.opportunities.show', $opportunity) }}" class="mr-2">Cancel</x-button>
                <x-button type="primary" submit>Update Opportunity</x-button>
            </div>
        </x-card>
    </form>
</x-layouts.admin>
