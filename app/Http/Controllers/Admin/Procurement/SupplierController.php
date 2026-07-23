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
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

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
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $categories = SupplierCategory::where('company_id', $companyId)->get();
        return view('admin.procurement.suppliers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

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
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
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