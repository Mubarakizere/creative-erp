<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaxController extends Controller
{
    protected $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Tax::class);
        return response()->json($this->taxService->getAllTaxes($request->user()->company_id ?? 1));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Tax::class);
        $data = $request->validate([
            'name' => 'required|string',
            'rate' => 'required|numeric',
            'type' => 'required|in:inclusive,exclusive',
            'is_active' => 'boolean'
        ]);
        $data['company_id'] = $request->user()->company_id ?? 1;

        $tax = $this->taxService->createTax($data);
        return response()->json($tax, 201);
    }

    public function show(Tax $tax)
    {
        Gate::authorize('view', $tax);
        return response()->json($tax);
    }

    public function update(Request $request, Tax $tax)
    {
        Gate::authorize('update', $tax);
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'rate' => 'sometimes|required|numeric',
            'type' => 'sometimes|required|in:inclusive,exclusive',
            'is_active' => 'boolean'
        ]);

        $tax = $this->taxService->updateTax($tax, $data);
        return response()->json($tax);
    }

    public function destroy(Tax $tax)
    {
        Gate::authorize('delete', $tax);
        $this->taxService->deleteTax($tax);
        return response()->json(null, 204);
    }
}
