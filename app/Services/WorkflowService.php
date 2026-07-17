<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\DB;
use Exception;

class WorkflowService
{
    /**
     * Create a new workflow template.
     */
    public function createWorkflow(array $data): ApprovalWorkflow
    {
        return DB::transaction(function () use ($data) {
            $workflow = ApprovalWorkflow::create([
                'company_id' => $data['company_id'] ?? auth()->user()?->company_id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'module' => $data['module'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $index => $stepData) {
                    $workflow->steps()->create([
                        'step_order' => $stepData['step_order'] ?? ($index + 1),
                        'name' => $stepData['name'],
                        'approver_role_id' => $stepData['approver_role_id'] ?? null,
                        'approver_user_id' => $stepData['approver_user_id'] ?? null,
                        'is_required' => $stepData['is_required'] ?? true,
                    ]);
                }
            }

            return $workflow;
        });
    }

    /**
     * Update an existing workflow template.
     */
    public function updateWorkflow(ApprovalWorkflow $workflow, array $data): ApprovalWorkflow
    {
        return DB::transaction(function () use ($workflow, $data) {
            $workflow->update([
                'name' => $data['name'] ?? $workflow->name,
                'description' => $data['description'] ?? $workflow->description,
                'module' => $data['module'] ?? $workflow->module,
                'is_active' => $data['is_active'] ?? $workflow->is_active,
            ]);

            if (isset($data['steps'])) {
                // Delete existing steps
                $workflow->steps()->delete();
                
                // Recreate steps
                foreach ($data['steps'] as $index => $stepData) {
                    $workflow->steps()->create([
                        'step_order' => $stepData['step_order'] ?? ($index + 1),
                        'name' => $stepData['name'],
                        'approver_role_id' => $stepData['approver_role_id'] ?? null,
                        'approver_user_id' => $stepData['approver_user_id'] ?? null,
                        'is_required' => $stepData['is_required'] ?? true,
                    ]);
                }
            }

            return $workflow;
        });
    }

    /**
     * Delete (soft) a workflow template.
     */
    public function deleteWorkflow(ApprovalWorkflow $workflow): bool
    {
        return $workflow->delete();
    }

    /**
     * Get the active workflow for a specific module.
     */
    public function getActiveWorkflowForModule(string $module, ?int $companyId = null): ?ApprovalWorkflow
    {
        $query = ApprovalWorkflow::where('module', $module)->where('is_active', true);
        
        if ($companyId) {
            $query->where(function($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            });
        }
        
        return $query->first();
    }
}
