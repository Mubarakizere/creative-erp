<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalWorkflow;
use App\Models\WorkflowStep;
use App\Models\ApprovalAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Events\WorkflowSubmitted;
use App\Events\WorkflowApproved;
use App\Events\WorkflowRejected;
use App\Events\WorkflowReturned;
use App\Events\WorkflowCancelled;
use App\Events\ApprovalAssigned;
use Exception;

class ApprovalService
{
    /**
     * Submit a record for approval.
     */
    public function submit(Model $approvable, ApprovalWorkflow $workflow, string $comment = null): Approval
    {
        if ($approvable->approval && !in_array($approvable->approval->status, ['Draft', 'Cancelled'])) {
            throw new Exception("This record already has an active approval process.");
        }

        $firstStep = $workflow->steps()->orderBy('step_order')->first();
        if (!$firstStep) {
            throw new Exception("The selected workflow has no steps.");
        }

        return DB::transaction(function () use ($approvable, $workflow, $firstStep, $comment) {
            $approval = Approval::create([
                'approvable_type' => get_class($approvable),
                'approvable_id' => $approvable->id,
                'approval_workflow_id' => $workflow->id,
                'current_step_id' => $firstStep->id,
                'status' => 'Pending Approval',
                'submitted_by' => auth()->id(),
                'submitted_at' => now(),
            ]);

            $this->logAction($approval, $firstStep, 'Submit', $comment);

            event(new WorkflowSubmitted($approval));
            event(new ApprovalAssigned($approval, $firstStep));

            return $approval;
        });
    }

    /**
     * Approve the current step.
     */
    public function approve(Approval $approval, string $comment = null): Approval
    {
        return DB::transaction(function () use ($approval, $comment) {
            $currentStep = $approval->currentStep;
            
            $this->logAction($approval, $currentStep, 'Approve', $comment);

            $nextStep = $this->getNextStep($approval, $currentStep);

            if ($nextStep) {
                // Advance to next step
                $approval->update([
                    'current_step_id' => $nextStep->id,
                    'status' => 'Pending Approval', // or 'In Review'
                ]);
                event(new ApprovalAssigned($approval, $nextStep));
            } else {
                // All steps completed
                $approval->update([
                    'status' => 'Approved',
                    'completed_at' => now(),
                ]);
                event(new WorkflowApproved($approval));
            }

            return $approval;
        });
    }

    /**
     * Reject the request.
     */
    public function reject(Approval $approval, string $comment = null): Approval
    {
        return DB::transaction(function () use ($approval, $comment) {
            $this->logAction($approval, $approval->currentStep, 'Reject', $comment);

            $approval->update([
                'status' => 'Rejected',
                'completed_at' => now(),
            ]);

            event(new WorkflowRejected($approval));

            return $approval;
        });
    }

    /**
     * Return the request for revision.
     */
    public function returnForRevision(Approval $approval, string $comment = null): Approval
    {
        return DB::transaction(function () use ($approval, $comment) {
            $this->logAction($approval, $approval->currentStep, 'Return', $comment);

            $approval->update([
                'status' => 'Returned for Revision',
            ]);

            event(new WorkflowReturned($approval));

            return $approval;
        });
    }
    
    /**
     * Resubmit a returned request.
     */
    public function resubmit(Approval $approval, string $comment = null): Approval
    {
        return DB::transaction(function () use ($approval, $comment) {
            $firstStep = $approval->workflow->steps()->orderBy('step_order')->first();
            
            $approval->update([
                'current_step_id' => $firstStep->id,
                'status' => 'Pending Approval',
                'submitted_at' => now(), // refresh submitted time
            ]);

            $this->logAction($approval, $firstStep, 'Resubmit', $comment);

            event(new WorkflowSubmitted($approval));
            event(new ApprovalAssigned($approval, $firstStep));
            
            return $approval;
        });
    }

    /**
     * Cancel the approval process.
     */
    public function cancel(Approval $approval, string $comment = null): Approval
    {
        return DB::transaction(function () use ($approval, $comment) {
            $this->logAction($approval, $approval->currentStep, 'Cancel', $comment);

            $approval->update([
                'status' => 'Cancelled',
                'completed_at' => now(),
            ]);

            event(new WorkflowCancelled($approval));

            return $approval;
        });
    }

    /**
     * Log an action in the audit trail.
     */
    protected function logAction(Approval $approval, ?WorkflowStep $step, string $action, ?string $comment): void
    {
        ApprovalAction::create([
            'approval_id' => $approval->id,
            'workflow_step_id' => $step?->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'comment' => $comment,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'acted_at' => now(),
        ]);
    }

    /**
     * Determine the next step in the workflow.
     */
    protected function getNextStep(Approval $approval, WorkflowStep $currentStep): ?WorkflowStep
    {
        return $approval->workflow->steps()
            ->where('step_order', '>', $currentStep->step_order)
            ->orderBy('step_order')
            ->first();
    }
}
