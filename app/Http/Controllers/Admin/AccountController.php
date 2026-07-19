<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\Crm\CustomerTimelineService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccountController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected AccountService $accountService)
    {
        $this->authorizeResource(Account::class, 'account');
    }

    public function index(Request $request)
    {
        $accounts = $this->accountService->getPaginatedAccounts($request->all());
        return view('admin.crm.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.accounts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $account = $this->accountService->createAccount($request->all());
        return redirect()->route('admin.crm.accounts.index')->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $account->load(['owner', 'industry', 'tags', 'contacts', 'opportunities']);
        return view('admin.crm.accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $companies = \App\Models\Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.crm.accounts.edit', compact('account', 'companies'));
    }

    public function update(Request $request, Account $account)
    {
        $this->accountService->updateAccount($account, $request->all());
        return redirect()->route('admin.crm.accounts.show', $account)->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->accountService->deleteAccount($account);
        return redirect()->route('admin.crm.accounts.index')->with('success', 'Account archived successfully.');
    }

    public function timeline(Account $account, CustomerTimelineService $timelineService)
    {
        $this->authorize('view', $account);
        
        $timelineEvents = $timelineService->getForModel($account);
        return view('admin.crm.partials.timeline', compact('timelineEvents'));
    }
}
