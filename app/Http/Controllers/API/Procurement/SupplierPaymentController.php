<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Services\Procurement\SupplierPaymentService;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    protected SupplierPaymentService $service;

    public function __construct(SupplierPaymentService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create', SupplierPayment::class);
        $data = $request->validate([
            'payment_number' => 'required|string|unique:supplier_payments,payment_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);
        return response()->json($this->service->create($data), 201);
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $this->authorize('view', $supplierPayment);
        $supplierPayment->load(['supplier', 'purchaseInvoice']);
        return response()->json($supplierPayment);
    }
}