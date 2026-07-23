<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\Procurement\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SupplierService $service;

    public function __construct(SupplierService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index(Request $request)
    {
        return response()->json($this->service->list($request->all()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:suppliers,code',
            'supplier_category_id' => 'nullable|exists:supplier_categories,id',
            'email' => 'nullable|email',
        ]);
        return response()->json($this->service->create($data), 201);
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['category', 'contacts', 'paymentTerm']);
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'nullable|email',
        ]);
        return response()->json($this->service->update($supplier, $data));
    }

    public function destroy(Supplier $supplier)
    {
        $this->service->delete($supplier);
        return response()->noContent();
    }
}