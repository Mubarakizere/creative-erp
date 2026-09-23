<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccountTypeController extends Controller
{
    use AuthorizesRequests;

    public const CATEGORIES = [
        'Asset' => 'Asset',
        'Liability' => 'Liability',
        'Equity' => 'Equity',
        'Revenue' => 'Revenue',
        'Expense' => 'Expense',
    ];

    /**
     * Display a listing of account types.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountType::class);

        $companyId = auth()->user()->company_id ?? (Company::first()?->id ?? 1);

        $query = AccountType::where('company_id', $companyId)
            ->withCount('chartOfAccounts');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $accountTypes = $query->orderBy('category')->orderBy('name')->get();

        $allTypes = AccountType::where('company_id', $companyId)->get();
        $stats = [
            'total' => $allTypes->count(),
            'assets' => $allTypes->where('category', 'Asset')->count(),
            'liabilities' => $allTypes->where('category', 'Liability')->count(),
            'equity' => $allTypes->where('category', 'Equity')->count(),
            'revenue' => $allTypes->where('category', 'Revenue')->count(),
            'expense' => $allTypes->where('category', 'Expense')->count(),
        ];

        return view('admin.account_types.index', compact('accountTypes', 'stats'));
    }

    /**
     * Show the form for creating a new account type.
     */
    public function create()
    {
        $this->authorize('create', AccountType::class);

        $categories = self::CATEGORIES;

        return view('admin.account_types.create', compact('categories'));
    }

    /**
     * Store a newly created account type.
     */
    public function store(Request $request)
    {
        $this->authorize('create', AccountType::class);

        $companyId = auth()->user()->company_id ?? (Company::first()?->id ?? 1);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Asset,Liability,Equity,Revenue,Expense',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $accountType = AccountType::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Account type created successfully.',
                'account_type' => $accountType,
            ]);
        }

        return redirect()->route('admin.account-types.index')
            ->with('success', "Account type '{$accountType->name}' created successfully.");
    }

    /**
     * Show the form for editing an account type.
     */
    public function edit(AccountType $accountType)
    {
        $this->authorize('update', $accountType);

        $accountType->loadCount('chartOfAccounts');
        $categories = self::CATEGORIES;

        return view('admin.account_types.edit', compact('accountType', 'categories'));
    }

    /**
     * Update the specified account type.
     */
    public function update(Request $request, AccountType $accountType)
    {
        $this->authorize('update', $accountType);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Asset,Liability,Equity,Revenue,Expense',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $accountType->update($validated);

        return redirect()->route('admin.account-types.index')
            ->with('success', "Account type '{$accountType->name}' updated successfully.");
    }

    /**
     * Remove the specified account type.
     */
    public function destroy(AccountType $accountType)
    {
        $this->authorize('delete', $accountType);

        $linkedCount = $accountType->chartOfAccounts()->count();
        if ($linkedCount > 0) {
            return back()->with('error', "Cannot delete account type '{$accountType->name}' because {$linkedCount} ledger account(s) are assigned to it.");
        }

        $name = $accountType->name;
        $accountType->delete();

        return redirect()->route('admin.account-types.index')
            ->with('success', "Account type '{$name}' deleted successfully.");
    }
}
