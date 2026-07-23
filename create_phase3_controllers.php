<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controllersDir = app_path('Http/Controllers/Admin/Procurement');
if (!File::exists($controllersDir)) {
    File::makeDirectory($controllersDir, 0755, true);
}

// 1. PurchaseInvoiceController
$invoiceControllerPath = $controllersDir . '/PurchaseInvoiceController.php';
$invoiceControllerStub = <<<PHP
<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    protected \$invoiceService;

    public function __construct(PurchaseInvoiceService \$invoiceService)
    {
        \$this->invoiceService = \$invoiceService;
    }

    public function index(Request \$request)
    {
        \$this->authorize('viewAny', PurchaseInvoice::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        \$query = PurchaseInvoice::where('company_id', \$companyId)->with('supplier');

        if (\$request->filled('search')) {
            \$query->where('invoice_number', 'like', "%{\$request->search}%");
        }

        \$invoices = \$query->latest()->paginate(15);
        return view('admin.procurement.invoices.index', compact('invoices'));
    }

    public function create(Request \$request)
    {
        \$this->authorize('create', PurchaseInvoice::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        
        \$poId = \$request->query('purchase_order_id');
        \$po = null;
        if (\$poId) {
            \$po = PurchaseOrder::with(['items.product', 'supplier'])->where('company_id', \$companyId)->findOrFail(\$poId);
        }

        \$suppliers = \App\Models\Supplier::where('company_id', \$companyId)->get();

        return view('admin.procurement.invoices.create', compact('po', 'suppliers'));
    }

    public function store(Request \$request)
    {
        \$this->authorize('create', PurchaseInvoice::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        \$validated = \$request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
        ]);

        \$data = [
            'company_id' => \$companyId,
            'invoice_number' => \$validated['invoice_number'],
            'supplier_id' => \$validated['supplier_id'],
            'purchase_order_id' => \$validated['purchase_order_id'] ?? null,
            'invoice_date' => \$validated['invoice_date'],
            'due_date' => \$validated['due_date'],
            'subtotal' => \$validated['subtotal'],
            'tax_amount' => \$validated['tax_amount'],
            'discount_amount' => \$validated['discount_amount'],
            'grand_total' => \$validated['grand_total'],
            'notes' => \$validated['notes'] ?? null,
            'status' => 'draft',
        ];

        \$invoice = \$this->invoiceService->create(\$data, \$validated['items']);

        // Update PO status to invoiced if all items are fully invoiced (simplified logic here: just mark it if passed)
        if (!empty(\$validated['purchase_order_id'])) {
            \$po = PurchaseOrder::find(\$validated['purchase_order_id']);
            // in a real scenario we'd calculate item by item
            \$po->status = 'completed'; 
            \$po->save();
        }

        return redirect()->route('admin.procurement.invoices.show', \$invoice->id)->with('success', 'Purchase Invoice created successfully.');
    }

    public function show(PurchaseInvoice \$invoice)
    {
        \$this->authorize('view', \$invoice);
        \$invoice->load(['items.product', 'supplier', 'payments']);
        return view('admin.procurement.invoices.show', compact('invoice'));
    }
}
PHP;

File::put(\$invoiceControllerPath, \$invoiceControllerStub);

// 2. SupplierPaymentController
\$paymentControllerPath = \$controllersDir . '/SupplierPaymentController.php';
\$paymentControllerStub = <<<PHP
<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\PurchaseInvoice;
use App\Services\Procurement\SupplierPaymentService;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    protected \$paymentService;

    public function __construct(SupplierPaymentService \$paymentService)
    {
        \$this->paymentService = \$paymentService;
    }

    public function index(Request \$request)
    {
        \$this->authorize('viewAny', SupplierPayment::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        \$query = SupplierPayment::where('company_id', \$companyId)->with(['supplier', 'invoice']);

        if (\$request->filled('search')) {
            \$query->where('payment_number', 'like', "%{\$request->search}%");
        }

        \$payments = \$query->latest()->paginate(15);
        return view('admin.procurement.payments.index', compact('payments'));
    }

    public function create(Request \$request)
    {
        \$this->authorize('create', SupplierPayment::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        
        \$invoiceId = \$request->query('purchase_invoice_id');
        \$invoice = null;
        if (\$invoiceId) {
            \$invoice = PurchaseInvoice::with('supplier')->where('company_id', \$companyId)->findOrFail(\$invoiceId);
        }

        \$suppliers = \App\Models\Supplier::where('company_id', \$companyId)->get();

        return view('admin.procurement.payments.create', compact('invoice', 'suppliers'));
    }

    public function store(Request \$request)
    {
        \$this->authorize('create', SupplierPayment::class);
        \$companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        \$validated = \$request->validate([
            'payment_number' => 'required|string|unique:supplier_payments,payment_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \$data = [
            'company_id' => \$companyId,
            'payment_number' => \$validated['payment_number'],
            'supplier_id' => \$validated['supplier_id'],
            'purchase_invoice_id' => \$validated['purchase_invoice_id'] ?? null,
            'payment_date' => \$validated['payment_date'],
            'amount' => \$validated['amount'],
            'payment_method' => \$validated['payment_method'],
            'reference_number' => \$validated['reference_number'] ?? null,
            'notes' => \$validated['notes'] ?? null,
        ];

        \$payment = \$this->paymentService->create(\$data);

        return redirect()->route('admin.procurement.payments.show', \$payment->id)->with('success', 'Supplier Payment recorded successfully.');
    }

    public function show(SupplierPayment \$payment)
    {
        \$this->authorize('view', \$payment);
        \$payment->load(['supplier', 'invoice']);
        return view('admin.procurement.payments.show', compact('payment'));
    }
}
PHP;

File::put(\$paymentControllerPath, \$paymentControllerStub);

echo "PurchaseInvoiceController and SupplierPaymentController created successfully.\n";

