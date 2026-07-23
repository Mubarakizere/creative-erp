<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $query = PurchaseOrder::where('company_id', $companyId)->with(['supplier']);
        
        $pos = $query->latest()->paginate(15);
        return view('admin.procurement.pos.index', compact('pos'));
    }

    public function show(PurchaseOrder $po)
    {
        $po->load(['supplier', 'items.product', 'requisition', 'receipts']);
        return view('admin.procurement.pos.show', compact('po'));
    }

    public function approve(PurchaseOrder $po)
    {
        // Typically involves PurchaseOrderService, but for simplicity here we just update status
        $po->update(['status' => 'approved']);
        return back()->with('success', 'Purchase Order approved successfully.');
    }
}