<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BranchService
{
    /**
     * Get paginated list of branches with search and filters.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Branch::query()->with('company');

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
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Company filter
        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Country filter
        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
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
     * Create a new branch.
     */
    public function create(array $data): Branch
    {
        $data['uuid'] = (string) Str::uuid();

        // Set audit fields
        if (auth()->check()) {
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
        }

        return Branch::create($data);
    }

    /**
     * Update an existing branch.
     */
    public function update(Branch $branch, array $data): Branch
    {
        // Set audit field
        if (auth()->check()) {
            $data['updated_by'] = auth()->id();
        }

        $branch->update($data);

        return $branch->fresh();
    }

    /**
     * Soft delete a branch.
     */
    public function delete(Branch $branch): bool
    {
        return $branch->delete();
    }

    /**
     * Restore a soft-deleted branch.
     */
    public function restore(Branch $branch): bool
    {
        return $branch->restore();
    }

    /**
     * Activate a branch.
     */
    public function activate(Branch $branch): Branch
    {
        $branch->update([
            'status' => 'active',
            'updated_by' => auth()->id(),
        ]);

        return $branch->fresh();
    }

    /**
     * Deactivate a branch.
     */
    public function deactivate(Branch $branch): Branch
    {
        $branch->update([
            'status' => 'inactive',
            'updated_by' => auth()->id(),
        ]);

        return $branch->fresh();
    }

    /**
     * Get distinct countries for filter dropdown.
     */
    public function getDistinctCountries(): array
    {
        return Branch::whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }
}
