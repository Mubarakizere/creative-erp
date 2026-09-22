<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Services\Finance\AccountingService;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $query = ChartOfAccount::with(['accountType', 'parent'])
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $category = $request->category;
            $query->whereHas('accountType', function ($q) use ($category) {
                $q->where('category', 'like', "%{$category}%");
            });
        }

        $accounts = $query->orderBy('code')->get();
        $accountTypes = AccountType::where('company_id', $companyId)->get();

        $allAccounts = ChartOfAccount::with('accountType')->where('company_id', $companyId)->get();
        $stats = [
            'total' => $allAccounts->count(),
            'assets' => $allAccounts->filter(fn($a) => str_contains(strtolower($a->accountType?->category ?? ''), 'asset'))->count(),
            'liabilities' => $allAccounts->filter(fn($a) => str_contains(strtolower($a->accountType?->category ?? ''), 'liab'))->count(),
            'equity' => $allAccounts->filter(fn($a) => str_contains(strtolower($a->accountType?->category ?? ''), 'equity'))->count(),
            'income' => $allAccounts->filter(fn($a) => str_contains(strtolower($a->accountType?->category ?? ''), 'rev') || str_contains(strtolower($a->accountType?->category ?? ''), 'inc'))->count(),
            'expenses' => $allAccounts->filter(fn($a) => str_contains(strtolower($a->accountType?->category ?? ''), 'exp'))->count(),
        ];

        return view('admin.finance.accounting.chart-of-accounts.index', compact('accounts', 'accountTypes', 'stats'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id ?? (\App\Models\Company::first()?->id ?? 1);
        $accountTypes = AccountType::where('company_id', $companyId)->get();

        if ($accountTypes->isEmpty()) {
            \Database\Seeders\AccountTypeSeeder::seedForCompany($companyId);
            $accountTypes = AccountType::where('company_id', $companyId)->get();
        }

        $parentAccounts = ChartOfAccount::where('company_id', $companyId)->get();

        return view('admin.finance.accounting.chart-of-accounts.create', compact('accountTypes', 'parentAccounts'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? (\App\Models\Company::first()?->id ?? 1);
        
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'code' => 'required|string|max:50|unique:chart_of_accounts,code,NULL,id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        
        $this->accountingService->createChartOfAccount($validated);

        return redirect()->route('admin.finance.accounting.chart-of-accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        $companyId = auth()->user()->company_id ?? $chartOfAccount->company_id ?? (\App\Models\Company::first()?->id ?? 1);
        $accountTypes = AccountType::where('company_id', $companyId)->get();

        if ($accountTypes->isEmpty()) {
            \Database\Seeders\AccountTypeSeeder::seedForCompany($companyId);
            $accountTypes = AccountType::where('company_id', $companyId)->get();
        }

        $parentAccounts = ChartOfAccount::where('company_id', $companyId)->where('id', '!=', $chartOfAccount->id)->get();

        return view('admin.finance.accounting.chart-of-accounts.edit', compact('chartOfAccount', 'accountTypes', 'parentAccounts'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'code' => 'required|string|max:50|unique:chart_of_accounts,code,' . $chartOfAccount->id . ',id,company_id,' . $companyId,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $this->accountingService->updateChartOfAccount($chartOfAccount, $validated);

        return redirect()->route('admin.finance.accounting.chart-of-accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_system) {
            return back()->with('error', 'Cannot delete a system account.');
        }
        
        // Ensure it has no ledger entries or children before deleting
        if ($chartOfAccount->generalLedgerEntries()->exists() || $chartOfAccount->children()->exists()) {
            return back()->with('error', 'Cannot delete account with existing transactions or child accounts.');
        }

        $chartOfAccount->delete();

        return redirect()->route('admin.finance.accounting.chart-of-accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
