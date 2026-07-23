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

        return view('admin.finance.settings.index', compact('paymentMethods', 'bankAccounts', 'taxes'));
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
}
