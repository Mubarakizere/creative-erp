<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class FinanceSettingsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        Gate::authorize('create', \App\Models\Payment::class); // Or whichever permission is appropriate for finance settings
        
        $companyId = session('company_id') ?? 1;

        $paymentMethods = PaymentMethod::where('company_id', $companyId)->get();
        $bankAccounts = BankAccount::where('company_id', $companyId)->get();
        $taxes = \App\Models\Tax::where('company_id', $companyId)->get();
        $budgetCategories = \App\Models\BudgetCategory::where('company_id', $companyId)->withCount('lines')->orderBy('name')->get();

        return view('admin.finance.settings.index', compact('paymentMethods', 'bankAccounts', 'taxes', 'budgetCategories'));
    }

    public function storePaymentMethod(Request $request)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PaymentMethod::create([
            'company_id' => session('company_id') ?? 1,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('admin.finance.settings')->with('success', 'Payment Method added successfully.');
    }

    public function destroyPaymentMethod($id)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $method = PaymentMethod::findOrFail($id);
        
        // Ensure it belongs to the user's company
        if ($method->company_id !== (session('company_id') ?? 1)) {
            abort(403);
        }

        $method->delete();

        return redirect()->route('admin.finance.settings')->with('success', 'Payment Method deleted successfully.');
    }

    public function storeBankAccount(Request $request)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $request->validate([
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
        ]);

        BankAccount::create([
            'company_id' => session('company_id') ?? 1,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'swift_code' => $request->swift_code,
            'currency' => $request->currency ?? 'USD',
            'is_active' => true,
        ]);

        return redirect()->route('admin.finance.settings')->with('success', 'Bank Account added successfully.');
    }

    public function destroyBankAccount($id)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $account = BankAccount::findOrFail($id);

        if ($account->company_id !== (session('company_id') ?? 1)) {
            abort(403);
        }

        $account->delete();

        return redirect()->route('admin.finance.settings')->with('success', 'Bank Account deleted successfully.');
    }

    public function storeTax(Request $request)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
        ]);

        \App\Models\Tax::create([
            'company_id' => session('company_id') ?? 1,
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
            'is_active' => true,
        ]);

        return redirect()->route('admin.finance.settings')->with('success', 'Tax added successfully.');
    }

    public function destroyTax($id)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $tax = \App\Models\Tax::findOrFail($id);

        if ($tax->company_id !== (session('company_id') ?? 1)) {
            abort(403);
        }

        $tax->delete();

        return redirect()->route('admin.finance.settings')->with('success', 'Tax deleted successfully.');
    }

    public function storeBudgetCategory(Request $request)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:expense,revenue',
            'description' => 'nullable|string|max:1000',
        ]);

        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $category = \App\Models\BudgetCategory::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cost Category added successfully.',
                'category' => $category,
            ]);
        }

        return redirect()->route('admin.finance.settings')->with('success', 'Cost Category added successfully.');
    }

    public function updateBudgetCategory(Request $request, $id)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $category = \App\Models\BudgetCategory::findOrFail($id);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        if ($category->company_id !== (int)$companyId && !auth()->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:expense,revenue',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.finance.settings')->with('success', 'Cost Category updated successfully.');
    }

    public function destroyBudgetCategory($id)
    {
        Gate::authorize('create', \App\Models\Payment::class);

        $category = \App\Models\BudgetCategory::withCount('lines')->findOrFail($id);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        if ($category->company_id !== (int)$companyId && !auth()->user()->hasRole('Super Admin')) {
            abort(403);
        }

        if ($category->lines_count > 0) {
            return redirect()->route('admin.finance.settings')
                ->with('error', "Cannot delete '{$category->name}' because it is assigned to {$category->lines_count} project budget activity line(s).");
        }

        $category->delete();

        return redirect()->route('admin.finance.settings')->with('success', 'Cost Category deleted successfully.');
    }
}
