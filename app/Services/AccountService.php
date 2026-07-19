<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AccountService
{
    public function getPaginatedAccounts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Account::with(['owner', 'industry', 'tags']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function createAccount(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['company_id'])) {
                $data['company_id'] = auth()->user()->company_id;
            }
            $data['created_by'] = auth()->id();
            
            $account = Account::create($data);

            if (!empty($data['tags'])) {
                $account->tags()->sync($data['tags']);
            }

            return $account;
        });
    }

    public function updateAccount(Account $account, array $data): Account
    {
        return DB::transaction(function () use ($account, $data) {
            $data['updated_by'] = auth()->id();
            
            $account->update($data);

            if (isset($data['tags'])) {
                $account->tags()->sync($data['tags']);
            }

            return $account;
        });
    }

    public function deleteAccount(Account $account): bool
    {
        return $account->delete();
    }
}
