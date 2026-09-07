<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\SupplierQuotation;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierQuotationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $query = SupplierQuotation::where('company_id', $companyId)->with(['supplier', 'purchaseRequisition', 'items']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhereHas('supplier', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $allQuotations = SupplierQuotation::where('company_id', $companyId)->with('items')->get();
        $totalQuotedValue = $allQuotations->sum(function($q) {
            return $q->items->sum(function($i) {
                return $i->total > 0 ? $i->total : (($i->quantity * $i->unit_price) - $i->discount + $i->tax);
            });
        });

        $stats = [
            'total' => $allQuotations->count(),
            'approved' => $allQuotations->where('status', 'approved')->count(),
            'draft' => $allQuotations->where('status', 'draft')->count(),
            'total_value' => $totalQuotedValue,
        ];

        $rfqs = $query->latest()->paginate(15);
        return view('admin.procurement.rfqs.index', compact('rfqs', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $requisitions = PurchaseRequisition::where('company_id', $companyId)->where('status', 'approved')->get();
        $suppliers = Supplier::where('company_id', $companyId)->get();
        $products = \App\Models\Product::where('company_id', $companyId)->with('unit')->get();
        $code = app(\App\Services\SequenceService::class)->generate('quotation', $companyId);

        $selectedPrId = $request->query('purchase_requisition_id');
        $selectedPr = null;
        $preloadedItems = [];
        if ($selectedPrId) {
            $selectedPr = PurchaseRequisition::with(['items.product.unit', 'project', 'requestedBy'])->find($selectedPrId);
            if ($selectedPr) {
                foreach ($selectedPr->items as $prItem) {
                    $preloadedItems[] = [
                        'product_id' => $prItem->product_id,
                        'quantity' => (float) $prItem->quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'tax' => 0,
                    ];
                }
            }
        }

        return view('admin.procurement.rfqs.create', compact('requisitions', 'suppliers', 'products', 'code', 'selectedPrId', 'selectedPr', 'preloadedItems'));
    }

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
}