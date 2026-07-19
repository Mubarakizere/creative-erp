<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    public function getPaginatedContacts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contact::with(['owner', 'account', 'tags']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function createContact(array $data): Contact
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['company_id'])) {
                $data['company_id'] = auth()->user()->company_id;
            }
            $data['created_by'] = auth()->id();
            
            $contact = Contact::create($data);

            if (!empty($data['tags'])) {
                $contact->tags()->sync($data['tags']);
            }

            return $contact;
        });
    }

    public function updateContact(Contact $contact, array $data): Contact
    {
        return DB::transaction(function () use ($contact, $data) {
            $data['updated_by'] = auth()->id();
            
            $contact->update($data);

            if (isset($data['tags'])) {
                $contact->tags()->sync($data['tags']);
            }

            return $contact;
        });
    }

    public function deleteContact(Contact $contact): bool
    {
        return $contact->delete();
    }
}
