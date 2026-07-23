<?php
namespace App\Services\Procurement;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function list(array $filters = [])
    {
        $query = PurchaseOrder::with(['supplier']);
        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }
        return $query->latest()->paginate(15);
    }

    public function create(array $data, array $items): PurchaseOrder
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            $data['status'] = 'draft';
            
            $po = PurchaseOrder::create($data);
            
            foreach ($items as $item) {
                $po->items()->create($item);
            }
            
            return $po;
        });
    }

    public function approve(PurchaseOrder $po)
    {
        if ($po->created_by === auth()->id()) {
            throw new \Exception("Cannot approve own purchase order");
        }
        $po->update(['status' => 'approved']);
        return $po;
    }
}