<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationApproval;
use App\Models\QuotationStatus;
use Illuminate\Support\Facades\DB;

class QuotationApprovalService
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function submitForApproval(Quotation $quotation, int $userId): void
    {
        if ($quotation->owner_id === $userId) {
            // Optional: The prompt says "Users cannot approve their own quotation." 
            // Submitting might be allowed, but let's check policies later.
        }

        DB::transaction(function () use ($quotation, $userId) {
            $status = QuotationStatus::where('name', 'Pending Approval')->first();
            if ($status) {
                $quotation->update(['status_id' => $status->id]);
            }
            
            // Integrate with WorkflowEngine
            $this->workflowService->initiateWorkflow($quotation, 'quotation', $userId);
        });
    }

    public function approve(Quotation $quotation, int $userId, string $comments = null): void
    {
        if ($quotation->owner_id === $userId) {
            throw new \Exception("You cannot approve your own quotation.");
        }

        DB::transaction(function () use ($quotation, $userId, $comments) {
            $status = QuotationStatus::where('name', 'Approved')->first();
            if ($status) {
                $quotation->update(['status_id' => $status->id]);
            }

            QuotationApproval::create([
                'quotation_id' => $quotation->id,
                'status' => 'approved',
                'comments' => $comments,
                'acted_by' => $userId,
                'acted_at' => now(),
            ]);

            // Complete workflow step if applicable
            // $this->workflowService->approveStep(...)
        });
    }

    public function reject(Quotation $quotation, int $userId, string $comments = null): void
    {
        DB::transaction(function () use ($quotation, $userId, $comments) {
            $status = QuotationStatus::where('name', 'Rejected')->first();
            if ($status) {
                $quotation->update(['status_id' => $status->id]);
            }

            QuotationApproval::create([
                'quotation_id' => $quotation->id,
                'status' => 'rejected',
                'comments' => $comments,
                'acted_by' => $userId,
                'acted_at' => now(),
            ]);
            
            // $this->workflowService->rejectStep(...)
        });
    }
}
