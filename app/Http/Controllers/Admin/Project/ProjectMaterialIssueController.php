<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProjectMaterialIssue;
use App\Http\Requests\Admin\StoreProjectMaterialIssueRequest;
use App\Services\Project\ProjectMaterialIssueService;
use App\Services\Inventory\InventoryValuationService;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class ProjectMaterialIssueController extends Controller
{
    protected ProjectMaterialIssueService $issueService;
    protected InventoryValuationService $valuationService;

    public function __construct(ProjectMaterialIssueService $issueService, InventoryValuationService $valuationService)
    {
        $this->issueService = $issueService;
        $this->valuationService = $valuationService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', ProjectMaterialIssue::class);

        $query = ProjectMaterialIssue::with(['project', 'warehouse', 'issuer'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $issues = $query->paginate(15);
        $projects = Project::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('admin.project-material-issues.index', compact('issues', 'projects', 'warehouses'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', ProjectMaterialIssue::class);

        $projects = Project::active()->get();
        $warehouses = Warehouse::active()->get();
        
        $products = collect();
        if ($request->filled('warehouse_id')) {
            // Get products with available stock in this warehouse
            $inventoryProductIds = Inventory::where('warehouse_id', $request->warehouse_id)
                ->where('available_quantity', '>', 0)
                ->pluck('product_id');
                
            $products = Product::whereIn('id', $inventoryProductIds)->get();
        }

        $issueNumber = 'PMI-' . strtoupper(substr(uniqid(), -6));
        $today = Carbon::today()->format('Y-m-d');

        return view('admin.project-material-issues.create', compact('projects', 'warehouses', 'products', 'issueNumber', 'today'));
    }

    public function store(StoreProjectMaterialIssueRequest $request)
    {
        $this->authorize('create', ProjectMaterialIssue::class);

        try {
            // First we need to check stock explicitly
            $warehouseId = $request->warehouse_id;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                
                $inventory = Inventory::where('product_id', $product->id)
                                      ->where('warehouse_id', $warehouseId)
                                      ->first();
                                      
                $available = $inventory ? $inventory->available_quantity : 0;
                
                if (!$product->allow_negative_stock && $qty > $available) {
                    throw new Exception("Insufficient stock for {$product->name}. Requested: {$qty}, Available: {$available}");
                }
            }

            $issue = $this->issueService->createIssue(
                $request->safe()->except('items'),
                $request->items
            );

            return redirect()->route('admin.project-material-issues.show', $issue->id)
                ->with('success', 'Project Material Issue created successfully and inventory deducted.');
                
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(ProjectMaterialIssue $projectMaterialIssue)
    {
        $this->authorize('view', $projectMaterialIssue);

        $projectMaterialIssue->load(['project', 'warehouse', 'issuer', 'items.product', 'items.materialRequestItem']);
        
        return view('admin.project-material-issues.show', compact('projectMaterialIssue'));
    }
}
