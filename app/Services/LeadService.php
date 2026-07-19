<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Comment;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadService
{
    public function getPaginatedLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Lead::with(['owner', 'leadSource', 'industry', 'tags']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createLead(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['company_id'])) {
                $data['company_id'] = auth()->user()->company_id;
            }
            $data['created_by'] = auth()->id();
            
            $lead = Lead::create($data);

            if (!empty($data['tags'])) {
                $lead->tags()->sync($data['tags']);
            }

            return $lead;
        });
    }

    public function updateLead(Lead $lead, array $data): Lead
    {
        return DB::transaction(function () use ($lead, $data) {
            $data['updated_by'] = auth()->id();
            
            $lead->update($data);

            if (isset($data['tags'])) {
                $lead->tags()->sync($data['tags']);
            }

            return $lead;
        });
    }

    public function deleteLead(Lead $lead): bool
    {
        return $lead->delete();
    }

    /**
     * Convert Lead to Account, Contact, and Opportunity
     */
    public function convertLead(Lead $lead, array $conversionData): array
    {
        return DB::transaction(function () use ($lead, $conversionData) {
            $companyId = $lead->company_id;
            $creatorId = auth()->id();

            // 1. Create or Find Account
            $account = null;
            if (!empty($conversionData['create_account']) && $lead->company_name) {
                $account = Account::create([
                    'company_id' => $companyId,
                    'name' => $lead->company_name,
                    'industry_id' => $lead->industry_id,
                    'website' => $lead->website,
                    'address' => $lead->address,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'owner_id' => $conversionData['owner_id'] ?? $lead->owner_id,
                    'created_by' => $creatorId,
                ]);
            } elseif (!empty($conversionData['account_id'])) {
                $account = Account::find($conversionData['account_id']);
            }

            // 2. Create or Find Contact
            $contact = null;
            if (!empty($conversionData['create_contact'])) {
                $contact = Contact::create([
                    'company_id' => $companyId,
                    'account_id' => $account?->id,
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'position' => $lead->title,
                    'address' => $lead->address,
                    'owner_id' => $conversionData['owner_id'] ?? $lead->owner_id,
                    'created_by' => $creatorId,
                ]);
            }

            // 3. Create Opportunity
            $opportunity = null;
            if (!empty($conversionData['create_opportunity']) && !empty($conversionData['opportunity_name'])) {
                $opportunity = Opportunity::create([
                    'company_id' => $companyId,
                    'account_id' => $account?->id,
                    'contact_id' => $contact?->id,
                    'pipeline_id' => $conversionData['pipeline_id'] ?? null,
                    'pipeline_stage_id' => $conversionData['pipeline_stage_id'] ?? null,
                    'name' => $conversionData['opportunity_name'],
                    'expected_revenue' => $lead->expected_value,
                    'probability' => $lead->probability,
                    'owner_id' => $conversionData['owner_id'] ?? $lead->owner_id,
                    'created_by' => $creatorId,
                ]);
            }

            // 4. Migrate History (Activities, Documents, Comments, Audit Logs, CRM Activities)
            // Default target for history is Opportunity, then Account, then Contact
            $targetType = null;
            $targetId = null;
            
            if ($opportunity) {
                $targetType = Opportunity::class;
                $targetId = $opportunity->id;
            } elseif ($account) {
                $targetType = Account::class;
                $targetId = $account->id;
            } elseif ($contact) {
                $targetType = Contact::class;
                $targetId = $contact->id;
            }

            if ($targetType && $targetId) {
                // Migrate Documents
                Document::where('documentable_type', Lead::class)
                        ->where('documentable_id', $lead->id)
                        ->update(['documentable_type' => $targetType, 'documentable_id' => $targetId]);

                // Migrate Comments (Notes/Discussions)
                Comment::where('commentable_type', Lead::class)
                       ->where('commentable_id', $lead->id)
                       ->update(['commentable_type' => $targetType, 'commentable_id' => $targetId]);

                // Migrate CRM Activities
                Activity::where('activityable_type', Lead::class)
                        ->where('activityable_id', $lead->id)
                        ->update(['activityable_type' => $targetType, 'activityable_id' => $targetId]);

                // Migrate Audit Logs
                ActivityLog::where('subject_type', Lead::class)
                           ->where('subject_id', $lead->id)
                           ->update(['subject_type' => $targetType, 'subject_id' => $targetId]);

                // Tags
                $tagIds = $lead->tags()->pluck('tags.id')->toArray();
                if (!empty($tagIds)) {
                    if ($opportunity) $opportunity->tags()->syncWithoutDetaching($tagIds);
                    if ($account) $account->tags()->syncWithoutDetaching($tagIds);
                    if ($contact) $contact->tags()->syncWithoutDetaching($tagIds);
                }
            }

            // 5. Update Lead Status
            $lead->update([
                'status' => 'Converted',
                'converted_account_id' => $account?->id,
                'converted_contact_id' => $contact?->id,
                'converted_opportunity_id' => $opportunity?->id,
                'converted_at' => now(),
                'updated_by' => $creatorId,
            ]);

            return [
                'account' => $account,
                'contact' => $contact,
                'opportunity' => $opportunity,
                'lead' => $lead,
            ];
        });
    }
}
