<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with(['project', 'assignee', 'parent']);
        
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('task_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(15);
        
        $projectQuery = Project::query();
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->company_id) {
            $projectQuery->where('company_id', auth()->user()->company_id);
        }
        $projects = $projectQuery->orderBy('name')->get();

        return view('admin.projects.tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Task::class);

        $projectQuery = Project::query();
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->company_id) {
            $projectQuery->where('company_id', auth()->user()->company_id);
        }
        $projects = $projectQuery->orderBy('name')->get();
        $selectedProject = null;
        
        if ($request->has('project_id')) {
            $selectedProject = Project::with('projectMembers.user')->findOrFail($request->project_id);
        }

        return view('admin.projects.tasks.create', compact('projects', 'selectedProject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $data = $request->validated();
        $project = Project::findOrFail($data['project_id']);
        $data['company_id'] = $project->company_id;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        try {
            $this->taskService->createTask($data);
            return redirect()->route('admin.projects.tasks.index', ['project_id' => $data['project_id']])
                ->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['project', 'assignee', 'parent', 'children', 'creator']);

        return view('admin.projects.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $task->load(['project.projectMembers.user']);
        
        $projectQuery = Project::query();
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->company_id) {
            $projectQuery->where('company_id', auth()->user()->company_id);
        }
        $projects = $projectQuery->orderBy('name')->get();

        return view('admin.projects.tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        try {
            $this->taskService->updateTask($task, $data);
            return redirect()->route('admin.projects.tasks.show', $task)
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $this->taskService->deleteTask($task);

        return redirect()->route('admin.projects.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Task $task)
    {
        $this->authorize('restore', $task);
        
        $this->taskService->restoreTask($task);

        return back()->with('success', 'Task restored successfully.');
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Task $task)
    {
        $this->authorize('create', Task::class);

        try {
            $newTask = $this->taskService->duplicateTask($task, ['created_by' => auth()->id(), 'updated_by' => auth()->id()]);
            return redirect()->route('admin.projects.tasks.edit', $newTask)
                ->with('success', 'Task duplicated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
