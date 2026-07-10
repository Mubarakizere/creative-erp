<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService
    ) {}

    /**
     * Display a listing of branches.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Branch::class);

        $branches = $this->branchService->list($request->only([
            'search', 'company_id', 'status', 'country', 'date_from', 'date_to', 'trashed',
        ]));

        $companies = Company::orderBy('name')->pluck('name', 'id')->toArray();
        $countries = $this->branchService->getDistinctCountries();

        return view('admin.branches.index', compact('branches', 'companies', 'countries'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create(): View
    {
        Gate::authorize('create', Branch::class);

        $companies = Company::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.branches.create', compact('companies'));
    }

    /**
     * Store a newly created branch.
     */
    public function store(StoreBranchRequest $request): RedirectResponse
    {
        Gate::authorize('create', Branch::class);

        $branch = $this->branchService->create($request->validated());

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch): View
    {
        Gate::authorize('view', $branch);

        $branch->load('company', 'creator', 'updater');

        return view('admin.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch): View
    {
        Gate::authorize('update', $branch);

        $companies = Company::where('status', 'active')->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.branches.edit', compact('branch', 'companies'));
    }

    /**
     * Update the specified branch.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        Gate::authorize('update', $branch);

        $this->branchService->update($branch, $request->validated());

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Soft delete the specified branch.
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        Gate::authorize('delete', $branch);

        $this->branchService->delete($branch);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    /**
     * Restore a soft-deleted branch.
     */
    public function restore(int $id): RedirectResponse
    {
        $branch = Branch::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $branch);

        $this->branchService->restore($branch);

        return redirect()
            ->route('admin.branches.show', $branch)
            ->with('success', 'Branch restored successfully.');
    }

    /**
     * Activate a branch.
     */
    public function activate(Branch $branch): RedirectResponse
    {
        Gate::authorize('activate', $branch);

        $this->branchService->activate($branch);

        return redirect()
            ->back()
            ->with('success', 'Branch activated successfully.');
    }

    /**
     * Deactivate a branch.
     */
    public function deactivate(Branch $branch): RedirectResponse
    {
        Gate::authorize('deactivate', $branch);

        $this->branchService->deactivate($branch);

        return redirect()
            ->back()
            ->with('success', 'Branch deactivated successfully.');
    }
}
