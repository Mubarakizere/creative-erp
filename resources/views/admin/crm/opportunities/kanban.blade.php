<x-layouts.admin title="Opportunities Kanban">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => 'Kanban Board']]; @endphp
    </x-slot:breadcrumbs>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Opportunities Kanban</h1>
            
            <form action="{{ route('admin.crm.opportunities.kanban') }}" method="GET" class="flex items-center">
                <select name="pipeline_id" onchange="this.form.submit()" class="rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($pipelines as $p)
                        <option value="{{ $p->id }}" {{ $pipelineId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="flex gap-2">
            <x-button type="ghost" href="{{ route('admin.crm.opportunities.index') }}">List View</x-button>
            @can('create', App\Models\Opportunity::class)
                <x-button type="primary" href="{{ route('admin.crm.opportunities.create') }}">Create Opportunity</x-button>
            @endcan
        </div>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-8 h-[calc(100vh-200px)] items-start">
        @foreach($kanbanData as $stage)
            <div class="flex-shrink-0 w-80 flex flex-col bg-gray-50/50 rounded-xl border border-gray-200 max-h-full">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-xl">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $stage['color'] ?? 'bg-blue-500' }}"></span>
                        {{ $stage['name'] }}
                    </h3>
                    <span class="bg-gray-100 text-gray-600 text-xs py-1 px-2 rounded-full font-medium">
                        {{ count($stage['opportunities']) }}
                    </span>
                </div>
                
                <div class="flex-1 p-4 overflow-y-auto space-y-3 min-h-[150px] kanban-stage" data-stage-id="{{ $stage['id'] }}">
                    @foreach($stage['opportunities'] as $opp)
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 cursor-move hover:shadow-md transition-shadow relative kanban-card" data-opportunity-id="{{ $opp->id }}">
                            <div class="flex justify-between items-start mb-2">
                                <a href="{{ route('admin.crm.opportunities.show', $opp) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $opp->name }}</a>
                            </div>
                            <div class="text-sm text-gray-500 mb-3">{{ $opp->account?->name ?? 'No Account' }}</div>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-700">${{ number_format($opp->expected_revenue, 2) }}</span>
                                <x-badge type="default" class="text-xs">{{ $opp->probability }}%</x-badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stages = document.querySelectorAll('.kanban-stage');
            
            stages.forEach(stage => {
                new Sortable(stage, {
                    group: 'shared', // set both lists to same group
                    animation: 150,
                    ghostClass: 'bg-gray-100',
                    onEnd: function (evt) {
                        const itemEl = evt.item;  // dragged HTMLElement
                        const toList = evt.to;    // target list
                        
                        const opportunityId = itemEl.getAttribute('data-opportunity-id');
                        const newStageId = toList.getAttribute('data-stage-id');
                        
                        if(evt.from !== toList) {
                            // Update the stage count numbers immediately (optimistic UI)
                            const oldBadge = evt.from.previousElementSibling.querySelector('span');
                            const newBadge = toList.previousElementSibling.querySelector('span');
                            oldBadge.innerText = parseInt(oldBadge.innerText) - 1;
                            newBadge.innerText = parseInt(newBadge.innerText) + 1;

                            // Send AJAX request
                            fetch(`/admin/crm/opportunities/${opportunityId}/kanban-update`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    stage_id: newStageId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(!data.success) {
                                    alert('Failed to update stage');
                                    // Revert could be implemented here
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Network error updating stage');
                            });
                        }
                    },
                });
            });
        });
    </script>
    @endpush
</x-layouts.admin>
