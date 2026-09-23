<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\Product;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseRequisition::class);
        $user = auth()->user();
        $companyId = session('company_id') ?? $user->company_id;

        $query = PurchaseRequisition::query()
            ->with(['requestedBy', 'project.company', 'department', 'items.product.unit', 'quotations', 'company']);

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
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('project_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('requestedBy', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $baseStatsQuery = PurchaseRequisition::query();
        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $baseStatsQuery->where('company_id', $companyId);
        }

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'pending' => (clone $baseStatsQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseStatsQuery)->where('status', 'approved')->count(),
            'draft' => (clone $baseStatsQuery)->where('status', 'draft')->count(),
        ];

        $requisitions = $query->latest()->paginate(15)->withQueryString();
        $projects = $user->accessibleProjects()->where('status', '!=', 'Closed')->get();
        $companies = Company::where('status', 'active')->orderBy('name')->get();

        return view('admin.procurement.requisitions.index', compact('requisitions', 'stats', 'projects', 'companies'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $user = auth()->user();

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $projects = $user->accessibleProjects()
            ->where('status', '!=', 'Closed')
            ->with(['company:id,name'])
            ->get();

        $selectedProject = $request->project_id ? $projects->firstWhere('id', $request->project_id) : null;
        if ($request->project_id && !$selectedProject) {
            $proj = Project::find($request->project_id);
            if ($proj && !$proj->isAssignedTo($user)) {
                abort(403);
            }
            $selectedProject = $proj;
        }

        $defaultCompanyId = $selectedProject?->company_id 
            ?? session('company_id') 
            ?? $user->company_id 
            ?? ($companies->first()?->id ?? 1);

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

        $products = Product::where('status', 'active')->with('unit')->get();
        if ($products->isEmpty()) {
            $products = Product::with('unit')->get();
        }

        try {
            $code = app(\App\Services\SequenceService::class)->generate('purchase_requisition', $defaultCompanyId);
        } catch (\Throwable $t) {
            $code = 'PR-' . date('Ymd') . '-' . rand(1000, 9999);
        }

        return view('admin.procurement.requisitions.create', compact(
            'products',
            'code',
            'companies',
            'projects',
            'projectsData',
            'selectedProject',
            'defaultCompanyId'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $user = auth()->user();

        $validated = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code',
            'company_id' => 'nullable|exists:companies,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'nullable|in:low,normal,high,urgent,Low,Normal,High,Urgent',
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,submitted',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.description' => 'nullable|string',
        ]);

        $companyId = $validated['company_id'] 
            ?? session('company_id') 
            ?? $user->company_id 
            ?? 1;

        if (!empty($validated['project_id'])) {
            $project = Project::findOrFail($validated['project_id']);
            if (!$project->isAssignedTo($user)) {
                abort(403, 'Unauthorized access to project.');
            }
        }

        $pr = PurchaseRequisition::create([
            'company_id' => $companyId,
            'project_id' => $validated['project_id'] ?? null,
            'code' => $validated['code'],
            'priority' => strtolower($validated['priority'] ?? 'normal'),
            'required_date' => $validated['required_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
            'requested_by' => $user->id,
            'created_by' => $user->id,
            'requisition_date' => now(),
        ]);

        foreach ($validated['items'] as $item) {
            $pr->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'description' => $item['description'] ?? null,
            ]);
        }

        return redirect()->route('admin.procurement.requisitions.index')->with('success', 'Purchase Requisition saved successfully.');
    }

    public function edit(PurchaseRequisition $requisition)
    {
        $this->authorize('update', $requisition);
        $user = auth()->user();

        $requisition->load(['items.product.unit', 'project', 'company']);

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

        $products = Product::where('status', 'active')->with('unit')->get();
        if ($products->isEmpty()) {
            $products = Product::with('unit')->get();
        }

        return view('admin.procurement.requisitions.edit', compact(
            'requisition',
            'products',
            'companies',
            'projects',
            'projectsData'
        ));
    }

    public function update(Request $request, PurchaseRequisition $requisition)
    {
        $this->authorize('update', $requisition);
        $user = auth()->user();

        $validated = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code,' . $requisition->id,
            'company_id' => 'nullable|exists:companies,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'nullable|in:low,normal,high,urgent,Low,Normal,High,Urgent',
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,submitted',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.description' => 'nullable|string',
        ]);

        $companyId = $validated['company_id'] ?? $requisition->company_id;

        if (!empty($validated['project_id'])) {
            $project = Project::findOrFail($validated['project_id']);
            if (!$project->isAssignedTo($user)) {
                abort(403, 'Unauthorized access to project.');
            }
        }

        $requisition->update([
            'company_id' => $companyId,
            'project_id' => $validated['project_id'] ?? null,
            'code' => $validated['code'],
            'priority' => strtolower($validated['priority'] ?? $requisition->priority ?? 'normal'),
            'required_date' => $validated['required_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
            'updated_by' => $user->id,
        ]);

        $requisition->items()->delete();
        foreach ($validated['items'] as $item) {
            $requisition->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'description' => $item['description'] ?? null,
            ]);
        }

        return redirect()->route('admin.procurement.requisitions.show', $requisition->id)->with('success', 'Purchase Requisition updated successfully.');
    }

    public function destroy(PurchaseRequisition $requisition)
    {
        $this->authorize('delete', $requisition);
        $requisition->delete();
        return redirect()->route('admin.procurement.requisitions.index')->with('success', 'Purchase Requisition deleted successfully.');
    }

    public function show(PurchaseRequisition $requisition)
    {
        $this->authorize('view', $requisition);
        $requisition->load(['items.product.unit', 'requestedBy', 'project.company', 'projectMaterialRequest', 'quotations.supplier', 'department', 'company']);
        return view('admin.procurement.requisitions.show', compact('requisition'));
    }

    public function approve(PurchaseRequisition $requisition)
    {
        $this->authorize('approve', $requisition);

        // Allow Super Admin and CEO to approve any requisition
        if ($requisition->requested_by === auth()->id() && !auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('CEO')) {
            return back()->with('error', 'You cannot approve your own requisition.');
        }

        $requisition->update(['status' => 'approved']);

        // Automatically generate RFQs / Supplier Quotations if none exist yet for this requisition
        if (!$requisition->quotations()->exists()) {
            $companyId = $requisition->company_id ?? session('company_id') ?? auth()->user()->company_id ?? 1;
            $suppliers = \App\Models\Supplier::where('company_id', $companyId)->get();

            if ($suppliers->isEmpty()) {
                $supplier = \App\Models\Supplier::firstOrCreate(
                    ['company_id' => $companyId, 'name' => 'General Supplier'],
                    ['code' => 'SUP-001', 'email' => 'supplier@example.com']
                );
                $suppliers = collect([$supplier]);
            }

            foreach ($suppliers as $supplier) {
                try {
                    $code = app(\App\Services\SequenceService::class)->generate('quotation', $companyId);
                } catch (\Throwable $t) {
                    $code = 'RFQ-' . date('Y') . '-' . str_pad($requisition->id . $supplier->id, 6, '0', STR_PAD_LEFT);
                }

                $rfq = \App\Models\SupplierQuotation::create([
                    'company_id' => $companyId,
                    'code' => $code,
                    'supplier_id' => $supplier->id,
                    'purchase_requisition_id' => $requisition->id,
                    'issue_date' => now(),
                    'valid_until' => now()->addDays(14),
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);

                foreach ($requisition->items as $item) {
                    $rfq->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.procurement.rfqs.index')->with('success', 'Requisition approved successfully and Request for Quotation (RFQ) generated.');
    }

    public function compare(PurchaseRequisition $requisition)
    {
        $requisition->load([
            'quotations.supplier',
            'quotations.items.product.unit',
            'items.product.unit',
            'requestedBy',
            'project',
            'department'
        ]);
        return view('admin.procurement.requisitions.compare', compact('requisition'));
    }

    public function acceptQuotation(PurchaseRequisition $requisition, \App\Models\SupplierQuotation $quotation)
    {
        // Approve the quotation and create PO
        $quotation->update(['status' => 'approved']);
        
        $po = \App\Models\PurchaseOrder::create([
            'company_id' => $quotation->company_id,
            'code' => app(\App\Services\SequenceService::class)->generate('purchase_order', $quotation->company_id),
            'supplier_id' => $quotation->supplier_id,
            'supplier_quotation_id' => $quotation->id,
            'order_date' => now(),
            'delivery_date' => now()->addDays(7),
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