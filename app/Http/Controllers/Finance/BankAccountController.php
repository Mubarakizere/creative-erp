<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BankAccountController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', BankAccount::class);
        $accounts = BankAccount::where('company_id', auth()->user()->company_id)->get();
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $this->authorize('create', BankAccount::class);
        
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        
        $account = BankAccount::create($data);
        return response()->json($account, 201);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        $bankAccount->update($request->all());
        return response()->json($bankAccount);
    }

    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);
        $bankAccount->delete();
        return response()->json(null, 204);
    }
}
