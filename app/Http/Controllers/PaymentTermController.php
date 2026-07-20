<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use App\Services\PaymentTermService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentTermController extends Controller
{
    protected $paymentTermService;

    public function __construct(PaymentTermService $paymentTermService)
    {
        $this->paymentTermService = $paymentTermService;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', PaymentTerm::class);
        return response()->json($this->paymentTermService->getAllPaymentTerms($request->user()->company_id ?? 1));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', PaymentTerm::class);
        $data = $request->validate([
            'name' => 'required|string',
            'days' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        $data['company_id'] = $request->user()->company_id ?? 1;

        $term = $this->paymentTermService->createPaymentTerm($data);
        return response()->json($term, 201);
    }

    public function show(PaymentTerm $paymentTerm)
    {
        Gate::authorize('view', $paymentTerm);
        return response()->json($paymentTerm);
    }

    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        Gate::authorize('update', $paymentTerm);
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'days' => 'sometimes|required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $term = $this->paymentTermService->updatePaymentTerm($paymentTerm, $data);
        return response()->json($term);
    }

    public function destroy(PaymentTerm $paymentTerm)
    {
        Gate::authorize('delete', $paymentTerm);
        $this->paymentTermService->deletePaymentTerm($paymentTerm);
        return response()->json(null, 204);
    }
}
