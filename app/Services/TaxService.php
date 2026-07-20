<?php

namespace App\Services;

use App\Models\Tax;
use Illuminate\Support\Collection;

class TaxService
{
    public function getAllTaxes(int $companyId): Collection
    {
        return Tax::where('company_id', $companyId)->get();
    }

    public function getActiveTaxes(int $companyId): Collection
    {
        return Tax::where('company_id', $companyId)->where('is_active', true)->get();
    }

    public function createTax(array $data): Tax
    {
        return Tax::create($data);
    }

    public function updateTax(Tax $tax, array $data): Tax
    {
        $tax->update($data);
        return $tax;
    }

    public function deleteTax(Tax $tax): bool
    {
        return $tax->delete();
    }
}
