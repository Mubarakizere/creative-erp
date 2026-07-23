<?php

$dir = 'app/Http/Controllers/Admin/Procurement';

// Update SupplierQuotationController
$quotationController = file_get_contents("$dir/SupplierQuotationController.php");
if (!str_contains($quotationController, 'public function show')) {
    $methods = <<<'EOT'
    public function show(SupplierQuotation $rfq)
    {
        $rfq->load(['supplier', 'purchaseRequisition', 'items.product']);
        return view('admin.procurement.rfqs.show', compact('rfq'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'code' => 'required|string|unique:supplier_quotations,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $rfq = SupplierQuotation::create([
            'company_id' => $companyId,
            'code' => $validated['code'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_requisition_id' => $validated['purchase_requisition_id'],
            'issue_date' => $validated['issue_date'],
            'valid_until' => $validated['valid_until'],
            'created_by' => auth()->id(),
            'status' => 'draft',
        ]);

        foreach ($validated['items'] as $item) {
            $discount = $item['discount'] ?? 0;
            $tax = $item['tax'] ?? 0;
            $total = ($item['quantity'] * $item['unit_price']) - $discount + $tax;
            
            $rfq->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
            ]);
        }

        return redirect()->route('admin.procurement.rfqs.index')->with('success', 'Quotation recorded successfully.');
    }
EOT;
    
    // Replace the existing store method
    $quotationController = preg_replace('/public function store.*?^    \}/ms', $methods, $quotationController);
    file_put_contents("$dir/SupplierQuotationController.php", $quotationController);
    echo "Updated SupplierQuotationController\n";
}

// Update PurchaseRequisitionController to add compare and accept methods
$reqController = file_get_contents("$dir/PurchaseRequisitionController.php");
if (!str_contains($reqController, 'public function compare')) {
    $methods = <<<'EOT'

    public function compare(PurchaseRequisition $requisition)
    {
        $requisition->load(['quotations.supplier', 'quotations.items.product']);
        return view('admin.procurement.requisitions.compare', compact('requisition'));
    }

    public function acceptQuotation(PurchaseRequisition $requisition, \App\Models\SupplierQuotation $quotation)
    {
        // Approve the quotation and create PO
        $quotation->update(['status' => 'approved']);
        
        $po = \App\Models\PurchaseOrder::create([
            'company_id' => $quotation->company_id,
            'code' => 'PO-' . time(),
            'supplier_id' => $quotation->supplier_id,
            'purchase_requisition_id' => $requisition->id,
            'order_date' => now(),
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($quotation->items as $item) {
            $po->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'tax' => $item->tax,
                'total' => $item->total,
            ]);
        }

        return redirect()->route('admin.procurement.pos.show', $po->id)->with('success', 'Quotation accepted and PO generated.');
    }
}
EOT;
    $reqController = preg_replace('/^\}$/m', $methods, $reqController);
    file_put_contents("$dir/PurchaseRequisitionController.php", $reqController);
    echo "Updated PurchaseRequisitionController\n";
}
