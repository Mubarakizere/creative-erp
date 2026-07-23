<?php
namespace App\Services\Procurement;

use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\DB;
use App\Services\WorkflowService;

class PurchaseRequisitionService
{
    public function list(array $filters = [])
    {
        $query = PurchaseRequisition::with(['requestedBy', 'department', 'project']);
        
        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }
        
        return $query->latest()->paginate(15);
    }

    public function create(array $data, array $items): PurchaseRequisition
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            $data['requested_by'] = auth()->id();
            $data['status'] = 'draft';
            
            $pr = PurchaseRequisition::create($data);
            
            foreach ($items as $item) {
                $pr->items()->create($item);
            }
            
            return $pr;
        });
    }

    public function submit(PurchaseRequisition $pr)
    {
        $pr->update(['status' => 'submitted']);
        // Trigger workflow
        return $pr;
    }

    public function approve(PurchaseRequisition $pr)
    {
        if ($pr->requested_by === auth()->id()) {
            throw new \Exception("Cannot approve own requisition");
        }
        $pr->update(['status' => 'approved']);
        return $pr;
    }
}