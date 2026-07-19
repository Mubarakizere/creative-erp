<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use App\Services\OpportunityService;
use App\Services\PipelineService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OpportunityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected OpportunityService $opportunityService,
        protected PipelineService $pipelineService
    ) {
        $this->authorizeResource(Opportunity::class, 'opportunity');
    }

    public function index(Request $request)
    {
        $opportunities = $this->opportunityService->getPaginatedOpportunities($request->all());
        return view('admin.crm.opportunities.index', compact('opportunities'));
    }

    public function kanban(Request $request)
    {
        $this->authorize('viewAny', Opportunity::class);
        $pipelineId = $request->get('pipeline_id') ?? $this->pipelineService->getDefaultPipeline()?->id;
        
        if (!$pipelineId) {
            return redirect()->route('admin.crm.opportunities.index')->with('error', 'No pipeline found.');
        }

        $kanbanData = $this->pipelineService->getKanbanData($pipelineId);
        $pipelines = $this->pipelineService->getPipelines();
        return view('admin.crm.opportunities.kanban', compact('kanbanData', 'pipelineId', 'pipelines'));
    }

    public function updateKanbanStage(Request $request, Opportunity $opportunity)
    {
        $this->authorize('update', $opportunity);
        $stageId = $request->input('stage_id');
        
        $this->pipelineService->updateOpportunityStage($opportunity, $stageId);
        
        return response()->json(['success' => true]);
    }

    public function create()
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        $pipelines = $this->pipelineService->getPipelines();
        return view('admin.crm.opportunities.create', compact('companies', 'pipelines'));
    }

    public function store(Request $request)
    {
        $opportunity = $this->opportunityService->createOpportunity($request->all());
        return redirect()->route('admin.crm.opportunities.index')->with('success', 'Opportunity created successfully.');
    }

    public function show(Opportunity $opportunity)
    {
        $opportunity->load(['owner', 'account', 'contact', 'pipeline', 'stage', 'tags']);
        return view('admin.crm.opportunities.show', compact('opportunity'));
    }

    public function edit(Opportunity $opportunity)
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        $pipelines = $this->pipelineService->getPipelines();
        return view('admin.crm.opportunities.edit', compact('opportunity', 'companies', 'pipelines'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $this->opportunityService->updateOpportunity($opportunity, $request->all());
        return redirect()->route('admin.crm.opportunities.show', $opportunity)->with('success', 'Opportunity updated successfully.');
    }

    public function destroy(Opportunity $opportunity)
    {
        $this->opportunityService->deleteOpportunity($opportunity);
        return redirect()->route('admin.crm.opportunities.index')->with('success', 'Opportunity archived successfully.');
    }
}
