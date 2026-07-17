<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Http\Requests\WorkflowRequest;
use App\Services\WorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkflowController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected WorkflowService $workflowService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', ApprovalWorkflow::class);
        $workflows = ApprovalWorkflow::with('company')->latest()->paginate(25);
        return view('admin.workflows.index', compact('workflows'));
    }

    public function create()
    {
        $this->authorize('create', ApprovalWorkflow::class);
        return view('admin.workflows.create');
    }

    public function store(WorkflowRequest $request)
    {
        $this->authorize('create', ApprovalWorkflow::class);
        $workflow = $this->workflowService->createWorkflow($request->validated());
        return redirect()->route('admin.workflows.index')->with('success', 'Workflow created successfully.');
    }

    public function show(ApprovalWorkflow $workflow)
    {
        $this->authorize('view', $workflow);
        $workflow->load('steps.role', 'steps.user');
        return view('admin.workflows.show', compact('workflow'));
    }

    public function edit(ApprovalWorkflow $workflow)
    {
        $this->authorize('update', $workflow);
        $workflow->load('steps');
        return view('admin.workflows.edit', compact('workflow'));
    }

    public function update(WorkflowRequest $request, ApprovalWorkflow $workflow)
    {
        $this->authorize('update', $workflow);
        $this->workflowService->updateWorkflow($workflow, $request->validated());
        return redirect()->route('admin.workflows.index')->with('success', 'Workflow updated successfully.');
    }

    public function destroy(ApprovalWorkflow $workflow)
    {
        $this->authorize('delete', $workflow);
        $this->workflowService->deleteWorkflow($workflow);
        return redirect()->route('admin.workflows.index')->with('success', 'Workflow deleted successfully.');
    }
}
