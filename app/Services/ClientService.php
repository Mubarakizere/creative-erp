<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    /**
     * Get a paginated list of clients with optional filters.
     *
     * @param array<string, mixed> $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Client::with(['company', 'branch']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_number', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['client_type'])) {
            $query->where('client_type', $filters['client_type']);
        }

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['trashed']) && $filters['trashed'] == 1) {
            $query->onlyTrashed();
        }

        return $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
    }

    /**
     * Create a new client.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Client
    {
        $data = $this->prepareClientData($data);
        $data['created_by'] = auth()->id();

        if (isset($data['logo_file'])) {
            $data['logo'] = $data['logo_file']->store('clients/logos', 'public');
            unset($data['logo_file']);
        }

        return Client::create($data);
    }

    /**
     * Update an existing client.
     *
     * @param array<string, mixed> $data
     */
    public function update(Client $client, array $data): Client
    {
        $data = $this->prepareClientData($data);
        $data['updated_by'] = auth()->id();

        if (isset($data['logo_file'])) {
            if ($client->logo) {
                Storage::disk('public')->delete($client->logo);
            }
            $data['logo'] = $data['logo_file']->store('clients/logos', 'public');
            unset($data['logo_file']);
        }

        $client->update($data);

        return $client->fresh();
    }

    /**
     * Soft delete a client.
     */
    public function delete(Client $client): bool
    {
        return (bool) $client->delete();
    }

    /**
     * Restore a soft-deleted client.
     */
    public function restore(Client $client): bool
    {
        return $client->restore();
    }

    /**
     * Activate a client.
     */
    public function activate(Client $client): bool
    {
        return $client->update(['status' => 'active', 'updated_by' => auth()->id()]);
    }

    /**
     * Deactivate a client.
     */
    public function deactivate(Client $client): bool
    {
        return $client->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
    }
    
    /**
     * Get distinct countries used by clients for filtering.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDistinctCountries()
    {
        return Client::whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');
    }

    /**
     * Prepare client data by generating display name and cleaning up fields based on type.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function prepareClientData(array $data): array
    {
        $type = $data['client_type'] ?? null;
        
        if ($type === 'Company') {
            $data['first_name'] = null;
            $data['last_name'] = null;
            $data['display_name'] = $data['company_name'];
        } elseif ($type === 'Individual') {
            $data['company_name'] = null;
            $data['tax_number'] = null;
            $data['registration_number'] = null;
            // The user requested to hide website, though not strictly forbid it in DB. Nulling it just in case.
            $data['website'] = null;
            
            $firstName = $data['first_name'] ?? '';
            $lastName = $data['last_name'] ?? '';
            $data['display_name'] = trim("$firstName $lastName");
        }
        
        return $data;
    }
}
