<?php

namespace Database\Seeders;

use App\Models\ApprovalWorkflow;
use Spatie\Permission\Models\Role;
use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        
        $workflow = ApprovalWorkflow::create([
            'name' => 'Default Document Approval',
            'description' => 'A basic two-step approval process for sensitive documents.',
            'module' => 'Documents',
            'is_active' => true,
        ]);

        WorkflowStep::create([
            'approval_workflow_id' => $workflow->id,
            'name' => 'Manager Review',
            'step_order' => 1,
            'approver_role_id' => $managerRole->id,
            'is_required' => true,
        ]);

        WorkflowStep::create([
            'approval_workflow_id' => $workflow->id,
            'name' => 'Final Admin Approval',
            'step_order' => 2,
            'approver_role_id' => $adminRole->id,
            'is_required' => true,
        ]);
        
        $timeWorkflow = ApprovalWorkflow::create([
            'name' => 'Timesheet Approval',
            'description' => 'Review of submitted timesheets.',
            'module' => 'TimeTracking',
            'is_active' => true,
        ]);

        WorkflowStep::create([
            'approval_workflow_id' => $timeWorkflow->id,
            'name' => 'Manager Approval',
            'step_order' => 1,
            'approver_role_id' => $managerRole->id,
            'is_required' => true,
        ]);
    }
}
