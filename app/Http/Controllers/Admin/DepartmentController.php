<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Department::class);

        $departments = $this->departmentService->list($request->only([
            'search', 'company_id', 'branch_id', 'status', 'date_from', 'date_to', 'trashed',
        ]));

        $companies = Company::orderBy('name')->pluck('name', 'id')->toArray();
        $branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.departments.index', compact('departments', 'companies', 'branches'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        Gate::authorize('create', Department::class);

        $companies = Company::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();
        $branches = Branch::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.departments.create', compact('companies', 'branches'));
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Gate::authorize('create', Department::class);

        $department = $this->departmentService->create($request->validated());

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): View
    {
        Gate::authorize('view', $department);

        $department->load('company', 'branch', 'creator', 'updater');

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        Gate::authorize('update', $department);

        $companies = Company::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();
        $branches = Branch::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.departments.edit', compact('department', 'companies', 'branches'));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        Gate::authorize('update', $department);

        $this->departmentService->update($department, $request->validated());

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Soft delete the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        Gate::authorize('delete', $department);

        $this->departmentService->delete($department);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Restore a soft-deleted department.
     */
    public function restore(int $id): RedirectResponse
    {
        $department = Department::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $department);

        $this->departmentService->restore($department);

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', 'Department restored successfully.');
    }

    /**
     * Activate a department.
     */
    public function activate(Department $department): RedirectResponse
    {
        Gate::authorize('activate', $department);

        $this->departmentService->activate($department);

        return redirect()
            ->back()
            ->with('success', 'Department activated successfully.');
    }

    /**
     * Deactivate a department.
     */
    public function deactivate(Department $department): RedirectResponse
    {
        Gate::authorize('deactivate', $department);

        $this->departmentService->deactivate($department);

        return redirect()
            ->back()
            ->with('success', 'Department deactivated successfully.');
    }

    /**
     * Get branches for a given company (AJAX endpoint for dependent dropdown).
     */
    public function getBranches(Company $company): JsonResponse
    {
        $branches = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($branches);
    }
}
