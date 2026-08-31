<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectMaterialRequestRequest;
use App\Http\Requests\Admin\UpdateProjectMaterialRequestRequest;
use App\Models\ProjectMaterialRequest;
use App\Models\Project;
use App\Models\Product;
use App\Models\Task;
use App\Services\ProjectMaterialRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectMaterialRequestController extends Controller
{
    public function __construct(
        protected ProjectMaterialRequestService $service
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', ProjectMaterialRequest::class);

        $query = ProjectMaterialRequest::with(['project', 'requestedBy', 'items'])
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('CEO')) {
            $query->whereHas('project', fn($q) => $q->accessibleBy(auth()->user()));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('request_number', 'like', "%{$request->search}%");
        }

        $requests = $query->paginate(15);
        $projects = auth()->user()->accessibleProjects()->where('status', '!=', 'Closed')->get();

        return view('admin.projects.material-requests.index', compact('requests', 'projects'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', ProjectMaterialRequest::class);

        $projects = auth()->user()->accessibleProjects()->where('status', '!=', 'Closed')->get();
        // Ideally we would load products via ajax depending on project/branch, but for this foundation we can load active products
        $products = Product::where('status', 'active')->get(); 
        
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;
        if ($selectedProject && !$selectedProject->isAssignedTo(auth()->user())) {
            abort(403);
        }
        $tasks = $selectedProject ? $selectedProject->tasks : collect();

        $companyId = auth()->user()->company_id ?? 1;
        $request_number = app(\App\Services\SequenceService::class)->generate('material_request', $companyId);

        return view('admin.projects.material-requests.create', compact('projects', 'products', 'selectedProject', 'tasks', 'request_number'));
    }

    public function store(StoreProjectMaterialRequestRequest $request)
    {
        Gate::authorize('create', ProjectMaterialRequest::class);

        $data = $request->validated();
        $project = Project::findOrFail($data['project_id']);
        if (!$project->isAssignedTo(auth()->user())) {
            abort(403);
        }

        $materialRequest = $this->service->create($data);

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request created successfully.');
    }

    public function show(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('view', $materialRequest);

        $materialRequest->load(['project', 'requestedBy', 'creator', 'updater', 'items.product', 'company', 'branch']);

        return view('admin.projects.material-requests.show', compact('materialRequest'));
    }

    public function edit(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('update', $materialRequest);

        $projects = auth()->user()->accessibleProjects()->where('status', '!=', 'Closed')->get();
        $products = Product::where('status', 'active')->get(); 
        $materialRequest->load('items', 'task');
        $tasks = $materialRequest->project_id ? Task::where('project_id', $materialRequest->project_id)->get() : collect();

        return view('admin.projects.material-requests.edit', compact('materialRequest', 'projects', 'products', 'tasks'));
    }

    public function update(UpdateProjectMaterialRequestRequest $request, ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('update', $materialRequest);

        $this->service->update($materialRequest, $request->validated());

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request updated successfully.');
    }

    public function destroy(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('delete', $materialRequest);

        $this->service->delete($materialRequest);

        return redirect()
            ->route('admin.material-requests.index')
            ->with('success', 'Material request deleted successfully.');
    }

    public function submit(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('submit', $materialRequest);

        $this->service->submit($materialRequest);

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request submitted successfully.');
    }

    public function approve(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('approve', $materialRequest);

        $this->service->approve($materialRequest);

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request approved successfully.');
    }

    public function reject(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('reject', $materialRequest);

        $this->service->reject($materialRequest);

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request rejected.');
    }

    public function cancel(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('cancel', $materialRequest);

        $this->service->cancel($materialRequest);

        return redirect()
            ->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Material request cancelled.');
    }

    public function convert(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('convertToProcurement', $materialRequest);

        try {
            $purchaseRequisition = $this->service->convertToPurchaseRequisition($materialRequest);

            return redirect()
                ->route('admin.material-requests.show', $materialRequest)
                ->with('success', "Converted to Purchase Requisition successfully (PR: {$purchaseRequisition->code}).");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.material-requests.show', $materialRequest)
                ->with('error', $e->getMessage());
        }
    }
}
