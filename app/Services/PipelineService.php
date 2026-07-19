<?php

namespace App\Services;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Services\CrmActivityService;

class PipelineService
{
    public function __construct(
        protected CrmActivityService $activityService
    ) {}
    public function getPipelines(): Collection
    {
        return Pipeline::with(['stages' => function ($q) {
            $q->orderBy('order');
        }])->get();
    }
    
    public function getDefaultPipeline(): ?Pipeline
    {
        return Pipeline::where('is_default', true)->first() ?? Pipeline::first();
    }

    public function getKanbanData(int $pipelineId): array
    {
        $pipeline = Pipeline::with(['stages' => function ($q) {
            $q->orderBy('order');
        }])->findOrFail($pipelineId);

        $stages = $pipeline->stages;
        $opportunities = Opportunity::with(['account', 'owner'])
            ->where('pipeline_id', $pipelineId)
            ->whereNotIn('status', ['Won', 'Lost'])
            ->get();

        $kanbanData = [];
        foreach ($stages as $stage) {
            $kanbanData[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'color' => $stage->color ?? 'bg-blue-500', // UI specific if needed
                'opportunities' => $opportunities->where('pipeline_stage_id', $stage->id)->values(),
            ];
        }

        return $kanbanData;
    }

    public function updateOpportunityStage(Opportunity $opportunity, int $newStageId): bool
    {
        return DB::transaction(function () use ($opportunity, $newStageId) {
            $stage = PipelineStage::findOrFail($newStageId);
            $oldStageName = $opportunity->stage ? $opportunity->stage->name : 'None';
            
            $opportunity->update([
                'pipeline_stage_id' => $stage->id,
                'probability' => $stage->probability, // Auto-update probability
                'updated_by' => auth()->id(),
            ]);

            // Log activity
            $this->activityService->createActivity([
                'company_id' => $opportunity->company_id,
                'activityable_type' => Opportunity::class,
                'activityable_id' => $opportunity->id,
                'type' => 'Other', // Or 'Stage Change' if supported
                'subject' => "Stage updated to {$stage->name}",
                'description' => "Opportunity stage was changed from {$oldStageName} to {$stage->name}.",
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            return true;
        });
    }
}
