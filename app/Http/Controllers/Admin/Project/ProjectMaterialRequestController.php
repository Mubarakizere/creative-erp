<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectMaterialRequestRequest;
use App\Http\Requests\Admin\UpdateProjectMaterialRequestRequest;
use App\Models\ProjectMaterialRequest;
use App\Models\Project;
use App\Models\Company;
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

        $query = ProjectMaterialRequest::with(['project', 'requestedBy', 'items', 'purchaseRequisition', 'company'])
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('CEO')) {
            $query->whereHas('project', fn($q) => $q->accessibleBy(auth()->user()));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('requestedBy', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate(15)->withQueryString();
        $projects = auth()->user()->accessibleProjects()->where('status', '!=', 'Closed')->get();

        $stats = [
            'total' => ProjectMaterialRequest::count(),
            'pending' => ProjectMaterialRequest::whereIn('status', ['Submitted', 'Under Review'])->count(),
            'approved' => ProjectMaterialRequest::where('status', 'Approved')->count(),
            'draft' => ProjectMaterialRequest::where('status', 'Draft')->count(),
        ];

        return view('admin.projects.material-requests.index', compact('requests', 'projects', 'stats'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', ProjectMaterialRequest::class);

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $projects = auth()->user()->accessibleProjects()
            ->where('status', '!=', 'Closed')
            ->with(['tasks' => function($q) {
                $q->select('id', 'project_id', 'name', 'task_code');
            }, 'company:id,name'])
            ->get();

        $products = Product::where('status', 'active')->get(); 
        
        $selectedProject = $request->project_id ? $projects->firstWhere('id', $request->project_id) : null;
        if ($request->project_id && !$selectedProject) {
            $proj = Project::find($request->project_id);
            if ($proj && !$proj->isAssignedTo(auth()->user())) {
                abort(403);
            }
            $selectedProject = $proj;
        }

        $tasks = $selectedProject ? $selectedProject->tasks : collect();

        $defaultCompanyId = $selectedProject?->company_id ?? auth()->user()->company_id ?? ($companies->first()?->id ?? 1);
        $request_number = app(\App\Services\SequenceService::class)->generate('material_request', $defaultCompanyId);

        $projectsData = $projects->mapWithKeys(function ($p) {
            return [$p->id => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->project_code,
                'company_id' => $p->company_id,
                'tasks' => $p->tasks->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'task_code' => $t->task_code,
                ])->values()->all(),
            ]];
        });

        return view('admin.projects.material-requests.create', compact(
            'projects',
            'companies',
            'products',
            'selectedProject',
            'tasks',
            'request_number',
            'projectsData',
            'defaultCompanyId'
        ));
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

        $materialRequest->load(['project', 'requestedBy', 'creator', 'updater', 'items.product', 'company', 'branch', 'task']);

        return view('admin.projects.material-requests.show', compact('materialRequest'));
    }

    public function edit(ProjectMaterialRequest $materialRequest)
    {
        Gate::authorize('update', $materialRequest);

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $projects = auth()->user()->accessibleProjects()
            ->where('status', '!=', 'Closed')
            ->with(['tasks' => function($q) {
                $q->select('id', 'project_id', 'name', 'task_code');
            }, 'company:id,name'])
            ->get();

        $products = Product::where('status', 'active')->get(); 
        $materialRequest->load(['items', 'task', 'company']);
        $tasks = $materialRequest->project_id ? Task::where('project_id', $materialRequest->project_id)->get() : collect();

        $projectsData = $projects->mapWithKeys(function ($p) {
            return [$p->id => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->project_code,
                'company_id' => $p->company_id,
                'tasks' => $p->tasks->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'task_code' => $t->task_code,
                ])->values()->all(),
            ]];
        });

        return view('admin.projects.material-requests.edit', compact(
            'materialRequest',
            'projects',
            'companies',
            'products',
            'tasks',
            'projectsData'
        ));
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
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.material-requests.show', $materialRequest)
                ->with('error', $e->getMessage());
        }
    }
}
