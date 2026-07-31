<x-layouts.admin title="Opportunities Kanban">
    <x-slot:breadcrumbs>
        @php $breadcrumbs = [['label' => 'CRM', 'url' => '#'], ['label' => 'Opportunities', 'url' => route('admin.crm.opportunities.index')], ['label' => 'Kanban Board']]; @endphp
    </x-slot:breadcrumbs>

    @can('viewAny', App\Models\Opportunity::class)
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Opportunities Kanban</h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">Drag and drop deals across pipeline stages.</p>
            </div>
            
            <form action="{{ route('admin.crm.opportunities.kanban') }}" method="GET" class="flex items-center ml-4">
                <select name="pipeline_id" onchange="this.form.submit()" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors shadow-sm text-gray-700">
                    @foreach($pipelines as $p)
                        <option value="{{ $p->id }}" {{ $pipelineId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.crm.opportunities.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                List View
            </a>
            @can('create', App\Models\Opportunity::class)
            <a href="{{ route('admin.crm.opportunities.create') }}" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:outline-none shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Opportunity
            </a>
            @endcan
        </div>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-8 h-[calc(100vh-220px)] items-start">
        @foreach($kanbanData as $stage)
            <div class="flex-shrink-0 w-80 flex flex-col bg-gray-50/50 rounded-2xl border border-gray-200/60 max-h-full shadow-sm">
                <div class="p-4 border-b border-gray-200/60 flex justify-between items-center bg-white rounded-t-2xl">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2 tracking-tight">
                        <span class="w-2.5 h-2.5 rounded-full {{ $stage['color'] ?? 'bg-blue-500' }}"></span>
                        {{ $stage['name'] }}
                    </h3>
                    <span class="bg-gray-100 text-gray-600 text-xs py-1 px-2.5 rounded-full font-bold">
                        {{ count($stage['opportunities']) }}
                    </span>
                </div>
                
                <div class="flex-1 p-4 overflow-y-auto space-y-3 min-h-[150px] kanban-stage" data-stage-id="{{ $stage['id'] }}">
                    @foreach($stage['opportunities'] as $opp)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200/60 cursor-grab hover:shadow-md hover:border-blue-300 transition-all relative kanban-card group" data-opportunity-id="{{ $opp->id }}">
                            <div class="flex justify-between items-start mb-2">
                                <a href="{{ route('admin.crm.opportunities.show', $opp) }}" class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">{{ $opp->name }}</a>
                            </div>
                            <div class="text-xs font-medium text-gray-500 mb-3">{{ $opp->account?->name ?? 'No Account' }}</div>
                            <div class="flex justify-between items-center mt-2 pt-3 border-t border-gray-100">
                                <span class="text-sm font-bold text-gray-700">${{ number_format($opp->expected_revenue, 2) }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                                    {{ $opp->probability }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 border border-red-200">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        <p class="text-sm text-gray-500 font-medium">You do not have permission to view the opportunities kanban board.</p>
    </div>
    @endcan

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
