<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class DepartmentService
{
    /**
     * Get paginated list of departments with search and filters.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Department::query()->with('company', 'branch');

        // Include trashed if requested
        if (! empty($filters['trashed'])) {
            $query->withTrashed();
        }

        // Search
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($bq) use ($search) {
                        $bq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Company filter
        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Branch filter
        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date filter
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(25)->withQueryString();
    }

    /**
     * Create a new department.
     */
    public function create(array $data): Department
    {
        $data['uuid'] = (string) Str::uuid();

        // Set audit fields
        if (auth()->check()) {
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
        }

        return Department::create($data);
    }

    /**
     * Update an existing department.
     */
    public function update(Department $department, array $data): Department
    {
        // Set audit field
        if (auth()->check()) {
            $data['updated_by'] = auth()->id();
        }

        $department->update($data);

        return $department->fresh();
    }

    /**
     * Soft delete a department.
     */
    public function delete(Department $department): bool
    {
        return $department->delete();
    }

    /**
     * Restore a soft-deleted department.
     */
    public function restore(Department $department): bool
    {
        return $department->restore();
    }

    /**
     * Activate a department.
     */
    public function activate(Department $department): Department
    {
        $department->update([
            'status' => 'active',
            'updated_by' => auth()->id(),
        ]);

        return $department->fresh();
    }

    /**
     * Deactivate a department.
     */
    public function deactivate(Department $department): Department
    {
        $department->update([
            'status' => 'inactive',
            'updated_by' => auth()->id(),
        ]);

        return $department->fresh();
    }
}
