<?php

namespace App\Services;

use App\Models\PaymentTerm;
use Illuminate\Support\Collection;

class PaymentTermService
{
    public function getAllPaymentTerms(int $companyId): Collection
    {
        return PaymentTerm::where('company_id', $companyId)->get();
    }

    public function getActivePaymentTerms(int $companyId): Collection
    {
        return PaymentTerm::where('company_id', $companyId)->where('is_active', true)->get();
    }

    public function createPaymentTerm(array $data): PaymentTerm
    {
        return PaymentTerm::create($data);
    }

    public function updatePaymentTerm(PaymentTerm $term, array $data): PaymentTerm
    {
        $term->update($data);
        return $term;
    }

    public function deletePaymentTerm(PaymentTerm $term): bool
    {
        return $term->delete();
    }
}
