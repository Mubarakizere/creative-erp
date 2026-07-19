<?php

namespace App\Services;

use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OpportunityService
{
    public function getPaginatedOpportunities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Opportunity::with(['owner', 'account', 'contact', 'pipeline', 'stage', 'tags']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filters['pipeline_id'])) {
            $query->where('pipeline_id', $filters['pipeline_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createOpportunity(array $data): Opportunity
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['company_id'])) {
                $data['company_id'] = auth()->user()->company_id;
            }
            $data['created_by'] = auth()->id();
            
            if (empty($data['pipeline_id'])) {
                $pipeline = \App\Models\Pipeline::where('is_default', true)->first() ?? \App\Models\Pipeline::first();
                if ($pipeline) {
                    $data['pipeline_id'] = $pipeline->id;
                    $firstStage = $pipeline->stages()->orderBy('order')->first();
                    if ($firstStage) {
                        $data['pipeline_stage_id'] = $firstStage->id;
                    }
                }
            }

            $opportunity = Opportunity::create($data);

            if (!empty($data['tags'])) {
                $opportunity->tags()->sync($data['tags']);
            }

            return $opportunity;
        });
    }

    public function updateOpportunity(Opportunity $opportunity, array $data): Opportunity
    {
        return DB::transaction(function () use ($opportunity, $data) {
            $data['updated_by'] = auth()->id();
            
            $opportunity->update($data);

            if (isset($data['tags'])) {
                $opportunity->tags()->sync($data['tags']);
            }

            return $opportunity;
        });
    }

    public function deleteOpportunity(Opportunity $opportunity): bool
    {
        return $opportunity->delete();
    }
}
