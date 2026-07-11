<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\Branch;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Client::class);

        $clients = $this->clientService->list($request->only([
            'search', 'status', 'company_id', 'branch_id', 'client_type', 'country', 'date_from', 'date_to', 'trashed',
        ]));

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $countries = $this->clientService->getDistinctCountries();

        return view('admin.clients.index', compact('clients', 'companies', 'branches', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Client::class);
        
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();

        return view('admin.clients.create', compact('companies', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Gate::authorize('create', Client::class);

        $client = $this->clientService->create($request->validated());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): View
    {
        Gate::authorize('view', $client);
        
        $client->load(['company', 'branch', 'creator', 'updater']);

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        Gate::authorize('update', $client);
        
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        // Depending on UI, you might want to load branches only for the selected company.
        // For simplicity, we load all active branches here, and filter via JS or load all.
        $branches = Branch::where('status', 'active')->orderBy('name')->get();

        return view('admin.clients.edit', compact('client', 'companies', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        Gate::authorize('update', $client);

        $this->clientService->update($client, $request->validated());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);

        $this->clientService->delete($client);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Restore a soft-deleted client.
     */
    public function restore(int $id): RedirectResponse
    {
        $client = Client::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $client);

        $this->clientService->restore($client);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client restored successfully.');
    }

    /**
     * Activate a client.
     */
    public function activate(Client $client): RedirectResponse
    {
        Gate::authorize('activate', $client);

        $this->clientService->activate($client);

        return redirect()
            ->back()
            ->with('success', 'Client activated successfully.');
    }

    /**
     * Deactivate a client.
     */
    public function deactivate(Client $client): RedirectResponse
    {
        Gate::authorize('deactivate', $client);

        $this->clientService->deactivate($client);

        return redirect()
            ->back()
            ->with('success', 'Client deactivated successfully.');
    }
}
