<?php
namespace App\Services\Procurement;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Supplier::with(['company', 'category', 'paymentTerm']);
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->latest()->paginate(15);
    }

    public function create(array $data): Supplier
    {
        $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
        $data['created_by'] = auth()->id();
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $data['updated_by'] = auth()->id();
        $supplier->update($data);
        return $supplier;
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}