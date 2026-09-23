<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\SupplierQuotation;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;

class SupplierQuotationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = session('company_id') ?? $user->company_id;

        $query = SupplierQuotation::query()
            ->with(['supplier', 'project.company', 'purchaseRequisition.project', 'items.product.unit', 'company']);

        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('project_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $allQuotationsQuery = SupplierQuotation::query();
        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $allQuotationsQuery->where('company_id', $companyId);
        }
        $allQuotations = $allQuotationsQuery->with('items')->get();

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

        $rfqs = $query->latest()->paginate(15)->withQueryString();
        $projects = $user->accessibleProjects()->where('status', '!=', 'Closed')->get();
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.procurement.rfqs.index', compact('rfqs', 'stats', 'projects', 'companies'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $projects = $user->accessibleProjects()
            ->where('status', '!=', 'Closed')
            ->with(['company:id,name'])
            ->get();

        $projectsData = $projects->mapWithKeys(function ($proj) {
            return [
                $proj->id => [
                    'id' => $proj->id,
                    'name' => $proj->name,
                    'project_code' => $proj->project_code ?? '',
                    'company_id' => $proj->company_id,
                ],
            ];
        });

        $selectedPrId = $request->query('purchase_requisition_id');
        $selectedPr = null;
        $preloadedItems = [];
        if ($selectedPrId) {
            $selectedPr = PurchaseRequisition::with(['items.product.unit', 'project', 'company', 'requestedBy'])->find($selectedPrId);
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

        $defaultCompanyId = $selectedPr?->company_id 
            ?? session('company_id') 
            ?? $user->company_id 
            ?? ($companies->first()?->id ?? 1);

        $defaultProjectId = $selectedPr?->project_id 
            ?? $request->query('project_id') 
            ?? '';

        $requisitionsQuery = PurchaseRequisition::where('status', 'approved')->with(['project', 'company', 'items.product.unit']);
        if ($defaultCompanyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $requisitionsQuery->where('company_id', $defaultCompanyId);
        }
        $requisitions = $requisitionsQuery->latest()->get();

        $requisitionsData = $requisitions->mapWithKeys(function ($req) {
            return [
                $req->id => [
                    'id' => $req->id,
                    'code' => $req->code,
                    'project_id' => $req->project_id,
                    'company_id' => $req->company_id,
                    'items' => $req->items->map(fn($it) => [
                        'product_id' => $it->product_id,
                        'quantity' => (float) $it->quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'tax' => 0,
                    ]),
                ],
            ];
        });

        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::orderBy('name')->get();
        }

        $products = Product::where('status', 'active')->with('unit')->get();
        if ($products->isEmpty()) {
            $products = Product::with('unit')->get();
        }

        try {
            $code = app(\App\Services\SequenceService::class)->generate('quotation', $defaultCompanyId);
        } catch (\Throwable $t) {
            $code = 'RFQ-' . date('Ymd') . '-' . rand(1000, 9999);
        }

        return view('admin.procurement.rfqs.create', compact(
            'requisitions',
            'requisitionsData',
            'suppliers',
            'products',
            'code',
            'companies',
            'projects',
            'projectsData',
            'selectedPrId',
            'selectedPr',
            'preloadedItems',
            'defaultCompanyId',
            'defaultProjectId'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'code' => 'required|string|unique:supplier_quotations,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'company_id' => 'nullable|exists:companies,id',
            'project_id' => 'nullable|exists:projects,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
            'lead_time_days' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        $pr = !empty($validated['purchase_requisition_id']) 
            ? PurchaseRequisition::find($validated['purchase_requisition_id']) 
            : null;

        $projectId = $validated['project_id'] ?? $pr?->project_id ?? null;
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            if (!$project->isAssignedTo($user)) {
                abort(403, 'Unauthorized access to project.');
            }
        }

        $companyId = $validated['company_id'] 
            ?? $pr?->company_id 
            ?? (isset($project) ? $project->company_id : null) 
            ?? session('company_id') 
            ?? $user->company_id 
            ?? 1;

        $rfq = SupplierQuotation::create([
            'company_id' => $companyId,
            'project_id' => $projectId,
            'code' => $validated['code'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_requisition_id' => $validated['purchase_requisition_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'valid_until' => $validated['valid_until'],
            'lead_time_days' => $validated['lead_time_days'] ?? $request->input('lead_time_days'),
            'created_by' => $user->id,
            'status' => $validated['status'] ?? $request->input('status', 'draft'),
        ]);

        foreach ($validated['items'] as $item) {
            $discount = (float)($item['discount'] ?? 0);
            $tax = (float)($item['tax'] ?? 0);
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

    public function show(SupplierQuotation $rfq)
    {
        $rfq->load(['supplier', 'purchaseRequisition.project', 'project.company', 'company', 'items.product.unit', 'creator']);
        return view('admin.procurement.rfqs.show', compact('rfq'));
    }

    public function edit(SupplierQuotation $rfq)
    {
        $user = auth()->user();
        $rfq->load(['items.product.unit', 'project', 'company', 'supplier', 'purchaseRequisition']);

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $projects = $user->accessibleProjects()
            ->where('status', '!=', 'Closed')
            ->with(['company:id,name'])
            ->get();

        $projectsData = $projects->mapWithKeys(function ($proj) {
            return [
                $proj->id => [
                    'id' => $proj->id,
                    'name' => $proj->name,
                    'project_code' => $proj->project_code ?? '',
                    'company_id' => $proj->company_id,
                ],
            ];
        });

        $requisitions = PurchaseRequisition::where('status', 'approved')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::orderBy('name')->get();
        }

        $products = Product::where('status', 'active')->with('unit')->get();
        if ($products->isEmpty()) {
            $products = Product::with('unit')->get();
        }

        return view('admin.procurement.rfqs.edit', compact(
            'rfq',
            'companies',
            'projects',
            'projectsData',
            'requisitions',
            'suppliers',
            'products'
        ));
    }

    public function update(Request $request, SupplierQuotation $rfq)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'code' => 'required|string|unique:supplier_quotations,code,' . $rfq->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'company_id' => 'nullable|exists:companies,id',
            'project_id' => 'nullable|exists:projects,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
            'lead_time_days' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        $projectId = $validated['project_id'] ?? $rfq->project_id;
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            if (!$project->isAssignedTo($user)) {
                abort(403, 'Unauthorized access to project.');
            }
        }

        $companyId = $validated['company_id'] ?? $rfq->company_id;

        $rfq->update([
            'code' => $validated['code'],
            'supplier_id' => $validated['supplier_id'],
            'company_id' => $companyId,
            'project_id' => $projectId,
            'purchase_requisition_id' => $validated['purchase_requisition_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'valid_until' => $validated['valid_until'],
            'lead_time_days' => $validated['lead_time_days'] ?? $request->input('lead_time_days'),
            'status' => $validated['status'] ?? $rfq->status ?? 'draft',
            'updated_by' => $user->id,
        ]);

        $rfq->items()->delete();
        foreach ($validated['items'] as $item) {
            $discount = (float)($item['discount'] ?? 0);
            $tax = (float)($item['tax'] ?? 0);
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

        return redirect()->route('admin.procurement.rfqs.show', $rfq->id)->with('success', 'Quotation updated successfully.');
    }

    public function destroy(SupplierQuotation $rfq)
    {
        $rfq->delete();
        return redirect()->route('admin.procurement.rfqs.index')->with('success', 'Quotation deleted successfully.');
    }
}