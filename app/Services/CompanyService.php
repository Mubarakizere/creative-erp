<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyService
{
    /**
     * Get paginated list of companies with search and filters.
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Company::query();

        // Include trashed if requested
        if (! empty($filters['trashed'])) {
            $query->withTrashed();
        }

        // Search
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
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
     * Create a new company.
     */
    public function create(array $data): Company
    {
        $data['uuid'] = (string) Str::uuid();
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        // Handle logo upload
        if (isset($data['logo']) && $data['logo'] !== null) {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        // Handle favicon upload
        if (isset($data['favicon']) && $data['favicon'] !== null) {
            $data['favicon'] = $this->uploadFavicon($data['favicon']);
        }

        // Handle working days
        if (isset($data['working_days']) && is_array($data['working_days'])) {
            $data['working_days'] = $data['working_days'];
        }

        // Set audit fields
        if (auth()->check()) {
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
        }

        return Company::create($data);
    }

    /**
     * Update an existing company.
     */
    public function update(Company $company, array $data): Company
    {
        // Handle logo upload
        if (isset($data['logo']) && $data['logo'] !== null) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $this->uploadLogo($data['logo']);
        } else {
            unset($data['logo']);
        }

        // Handle favicon upload
        if (isset($data['favicon']) && $data['favicon'] !== null) {
            // Delete old favicon
            if ($company->favicon) {
                Storage::disk('public')->delete($company->favicon);
            }
            $data['favicon'] = $this->uploadFavicon($data['favicon']);
        } else {
            unset($data['favicon']);
        }

        // Regenerate slug if name changed
        if (isset($data['name']) && $data['name'] !== $company->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $company->id);
        }

        // Set audit field
        if (auth()->check()) {
            $data['updated_by'] = auth()->id();
        }

        $company->update($data);

        return $company->fresh();
    }

    /**
     * Soft delete a company.
     */
    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    /**
     * Restore a soft-deleted company.
     */
    public function restore(Company $company): bool
    {
        return $company->restore();
    }

    /**
     * Activate a company.
     */
    public function activate(Company $company): Company
    {
        $company->update([
            'status' => 'active',
            'updated_by' => auth()->id(),
        ]);

        return $company->fresh();
    }

    /**
     * Deactivate a company.
     */
    public function deactivate(Company $company): Company
    {
        $company->update([
            'status' => 'inactive',
            'updated_by' => auth()->id(),
        ]);

        return $company->fresh();
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo($file): string
    {
        return $file->store('companies/logos', 'public');
    }

    /**
     * Upload company favicon.
     */
    public function uploadFavicon($file): string
    {
        return $file->store('companies/favicons', 'public');
    }

    /**
     * Generate a unique slug from the company name.
     */
    public function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = Company::withTrashed()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;

            $query = Company::withTrashed()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Get distinct countries for filter dropdown.
     */
    public function getDistinctCountries(): array
    {
        return Company::whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }
}
