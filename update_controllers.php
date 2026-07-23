<?php

$dir = 'app/Http/Controllers/Api/Procurement';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$controllers = [
    'SupplierController.php' => <<<'EOT'
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
EOT,
    'PurchaseRequisitionController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Services\Procurement\PurchaseRequisitionService;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    protected PurchaseRequisitionService $service;

    public function __construct(PurchaseRequisitionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseRequisition::class);
        return response()->json($this->service->list($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $data = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);
        $items = $data['items'];
        unset($data['items']);
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('view', $purchaseRequisition);
        $purchaseRequisition->load(['items.product', 'requestedBy', 'department', 'project']);
        return response()->json($purchaseRequisition);
    }

    public function approve(PurchaseRequisition $purchaseRequisition)
    {
        $this->authorize('approve', $purchaseRequisition);
        return response()->json($this->service->approve($purchaseRequisition));
    }
}
EOT,
    'PurchaseOrderController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\Procurement\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $service;

    public function __construct(PurchaseOrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseOrder::class);
        return response()->json($this->service->list($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);
        $data = $request->validate([
            'code' => 'required|string|unique:purchase_orders,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        $items = $data['items'];
        unset($data['items']);
        // calculate item total
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }
        $data['subtotal'] = collect($items)->sum('total');
        $data['grand_total'] = $data['subtotal'];

        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load(['items.product', 'supplier', 'quotation']);
        return response()->json($purchaseOrder);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);
        return response()->json($this->service->approve($purchaseOrder));
    }
}
EOT,
    'GoodsReceiptController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    protected GoodsReceiptService $service;

    public function __construct(GoodsReceiptService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create', GoodsReceipt::class);
        $data = $request->validate([
            'code' => 'required|string|unique:goods_receipts,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
        ]);
        $items = $data['items'];
        unset($data['items']);
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load(['items.product', 'supplier', 'purchaseOrder']);
        return response()->json($goodsReceipt);
    }
}
EOT,
    'PurchaseInvoiceController.php' => <<<'EOT'
<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    protected PurchaseInvoiceService $service;

    public function __construct(PurchaseInvoiceService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseInvoice::class);
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        $items = $data['items'];
        unset($data['items']);
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }
        $data['subtotal'] = collect($items)->sum('total');
        $data['grand_total'] = $data['subtotal'];
        
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $this->authorize('view', $purchaseInvoice);
        $purchaseInvoice->load(['items.product', 'supplier']);
        return response()->json($purchaseInvoice);
    }
}
EOT,
    'SupplierPaymentController.php' => <<<'EOT'
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
EOT,
];

foreach ($controllers as $filename => $content) {
    file_put_contents("$dir/$filename", $content);
    echo "Created $filename\n";
}

// Add Routes to routes/api.php
$apiRoutesPath = 'routes/api.php';
$apiContent = file_get_contents($apiRoutesPath);

$routesToInject = <<<'EOT'

    // Procurement
    Route::prefix('procurement')->group(function () {
        Route::apiResource('suppliers', \App\Http\Controllers\Api\Procurement\SupplierController::class);
        Route::apiResource('purchase-requisitions', \App\Http\Controllers\Api\Procurement\PurchaseRequisitionController::class)->only(['index', 'store', 'show']);
        Route::post('purchase-requisitions/{purchaseRequisition}/approve', [\App\Http\Controllers\Api\Procurement\PurchaseRequisitionController::class, 'approve']);
        Route::apiResource('purchase-orders', \App\Http\Controllers\Api\Procurement\PurchaseOrderController::class)->only(['index', 'store', 'show']);
        Route::post('purchase-orders/{purchaseOrder}/approve', [\App\Http\Controllers\Api\Procurement\PurchaseOrderController::class, 'approve']);
        Route::apiResource('goods-receipts', \App\Http\Controllers\Api\Procurement\GoodsReceiptController::class)->only(['store', 'show']);
        Route::apiResource('purchase-invoices', \App\Http\Controllers\Api\Procurement\PurchaseInvoiceController::class)->only(['store', 'show']);
        Route::apiResource('supplier-payments', \App\Http\Controllers\Api\Procurement\SupplierPaymentController::class)->only(['store', 'show']);
    });
EOT;

if (!str_contains($apiContent, "prefix('procurement')")) {
    // Insert before the last });
    $pos = strrpos($apiContent, "});");
    if ($pos !== false) {
        $apiContent = substr_replace($apiContent, $routesToInject . "\n", $pos, 0);
        file_put_contents($apiRoutesPath, $apiContent);
        echo "Added Procurement routes to api.php\n";
    }
}
