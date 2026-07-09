<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    /**
     * Display a listing of companies.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Company::class);

        $companies = $this->companyService->list($request->only([
            'search', 'status', 'country', 'date_from', 'date_to', 'trashed',
        ]));

        $countries = $this->companyService->getDistinctCountries();

        return view('admin.companies.index', compact('companies', 'countries'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        Gate::authorize('create', Company::class);

        return view('admin.companies.create');
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Company::class);

        $company = $this->companyService->create($request->validated());

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): View
    {
        Gate::authorize('view', $company);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View
    {
        Gate::authorize('update', $company);

        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        Gate::authorize('update', $company);

        $this->companyService->update($company, $request->validated());

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Soft delete the specified company.
     */
    public function destroy(Company $company): RedirectResponse
    {
        Gate::authorize('delete', $company);

        $this->companyService->delete($company);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Restore a soft-deleted company.
     */
    public function restore(int $id): RedirectResponse
    {
        $company = Company::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $company);

        $this->companyService->restore($company);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Company restored successfully.');
    }

    /**
     * Activate a company.
     */
    public function activate(Company $company): RedirectResponse
    {
        Gate::authorize('activate', $company);

        $this->companyService->activate($company);

        return redirect()
            ->back()
            ->with('success', 'Company activated successfully.');
    }

    /**
     * Deactivate a company.
     */
    public function deactivate(Company $company): RedirectResponse
    {
        Gate::authorize('deactivate', $company);

        $this->companyService->deactivate($company);

        return redirect()
            ->back()
            ->with('success', 'Company deactivated successfully.');
    }

    /**
     * Show company settings page.
     */
    public function settings(Company $company): View
    {
        Gate::authorize('update', $company);

        return view('admin.companies.settings', compact('company'));
    }
}
