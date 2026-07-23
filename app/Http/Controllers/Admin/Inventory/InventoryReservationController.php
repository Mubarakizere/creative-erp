<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryReservation;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\Inventory\InventoryEngine;
use Illuminate\Http\Request;

class InventoryReservationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', InventoryReservation::class);
        $companyId = session('company_id') ?? 1;

        $reservations = InventoryReservation::where('company_id', $companyId)
            ->with(['product', 'reference', 'warehouse', 'zone'])
            ->latest()
            ->paginate(15);

        return view('admin.inventory.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $this->authorize('create', InventoryReservation::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)
            ->where('status', 'active')
            ->with('zones')
            ->get();
            
        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();
            
        // Get references for the dropdowns
        $quotations = Quotation::where('company_id', $companyId)->latest()->take(50)->get();
        $invoices = Invoice::where('company_id', $companyId)->latest()->take(50)->get();
        $projects = Project::where('company_id', $companyId)->latest()->take(50)->get();

        return view('admin.inventory.reservations.create', compact('warehouses', 'products', 'quotations', 'invoices', 'projects'));
    }

    public function store(Request $request, InventoryEngine $engine)
    {
        $this->authorize('create', InventoryReservation::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'zone_id' => 'nullable|exists:warehouse_zones,id',
            'quantity' => 'required|numeric|min:0.01',
            'reference_type' => 'required|string|in:Quotation,Invoice,Project',
            'reference_id' => 'required|uuid',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
        
        // Resolve reference
        $referenceModel = "App\\Models\\" . $validated['reference_type'];
        $reference = $referenceModel::findOrFail($validated['reference_id']);

        try {
            $engine->reserve(
                $product,
                $warehouse,
                $validated['quantity'],
                $reference,
                $validated['expires_at'] ?? null,
                null, // variantId
                $validated['zone_id'] ?? null
            );
            
            return redirect()->route('admin.inventory.reservations.index')
                ->with('success', 'Stock reserved successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
    
    public function release(InventoryReservation $reservation, InventoryEngine $engine)
    {
        $this->authorize('delete', $reservation); // Or specific release permission
        
        try {
            $engine->releaseReservation($reservation);
            return back()->with('success', 'Reservation released successfully. Stock has been returned to available.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to release reservation: ' . $e->getMessage());
        }
    }
}
