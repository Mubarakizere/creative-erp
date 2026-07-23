<?php

$dir = 'app/Http/Controllers/Admin/Procurement';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$controllers = [
    'SupplierController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;

        $query = Supplier::where('company_id', $companyId)->with(['category', 'paymentTerm']);
        
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $suppliers = $query->latest()->paginate(15);
        return view('admin.procurement.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;
        $categories = SupplierCategory::where('company_id', $companyId)->get();
        return view('admin.procurement.suppliers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code',
            'supplier_category_id' => 'nullable|exists:supplier_categories,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_preferred' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = auth()->id();
        $validated['is_preferred'] = $request->boolean('is_preferred');

        Supplier::create($validated);

        return redirect()->route('admin.procurement.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        $companyId = session('company_id') ?? auth()->user()->company_id;
        $categories = SupplierCategory::where('company_id', $companyId)->get();
        return view('admin.procurement.suppliers.edit', compact('supplier', 'categories'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code,' . $supplier->id,
            'supplier_category_id' => 'nullable|exists:supplier_categories,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_preferred' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['is_preferred'] = $request->boolean('is_preferred');

        $supplier->update($validated);

        return redirect()->route('admin.procurement.suppliers.index')->with('success', 'Supplier updated successfully.');
    }
}
EOT,
    'PurchaseRequisitionController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;

        $query = PurchaseRequisition::where('company_id', $companyId)->with(['requestedBy']);
        
        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$request->search}%");
        }

        $requisitions = $query->latest()->paginate(15);
        return view('admin.procurement.requisitions.index', compact('requisitions'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->get();
        return view('admin.procurement.requisitions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id;

        $validated = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code',
            'status' => 'required|in:draft,submitted',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.description' => 'nullable|string',
        ]);

        $pr = PurchaseRequisition::create([
            'company_id' => $companyId,
            'code' => $validated['code'],
            'status' => $validated['status'],
            'requested_by' => auth()->id(),
            'created_by' => auth()->id(),
            'requisition_date' => now(),
        ]);

        foreach ($validated['items'] as $item) {
            $pr->items()->create($item);
        }

        return redirect()->route('admin.procurement.requisitions.index')->with('success', 'Purchase Requisition saved successfully.');
    }

    public function show(PurchaseRequisition $requisition)
    {
        $this->authorize('view', $requisition);
        $requisition->load(['items.product', 'requestedBy']);
        return view('admin.procurement.requisitions.show', compact('requisition'));
    }

    public function approve(PurchaseRequisition $requisition)
    {
        $this->authorize('approve', $requisition);
        
        if ($requisition->requested_by === auth()->id()) {
            return back()->with('error', 'You cannot approve your own requisition.');
        }

        $requisition->update(['status' => 'approved']);
        return back()->with('success', 'Requisition approved successfully.');
    }
}
EOT,
    'SupplierQuotationController.php' => <<<'EOT'
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
        $companyId = session('company_id') ?? auth()->user()->company_id;
        $query = SupplierQuotation::where('company_id', $companyId)->with(['supplier', 'purchaseRequisition']);
        
        if ($request->filled('search')) {
            $query->where('quotation_number', 'like', "%{$request->search}%");
        }

        $rfqs = $query->latest()->paginate(15);
        return view('admin.procurement.rfqs.index', compact('rfqs'));
    }

    public function create(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id;
        $requisitions = PurchaseRequisition::where('company_id', $companyId)->where('status', 'approved')->get();
        $suppliers = Supplier::where('company_id', $companyId)->get();
        return view('admin.procurement.rfqs.create', compact('requisitions', 'suppliers'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id;

        $validated = $request->validate([
            'quotation_number' => 'required|string|unique:supplier_quotations,quotation_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        SupplierQuotation::create($validated);

        return redirect()->route('admin.procurement.rfqs.index')->with('success', 'RFQ created successfully.');
    }
}
EOT,
];

foreach ($controllers as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Created $filename\n";
}
