<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectMember;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            return;
        }

        foreach ($projects as $project) {
            $activeMembers = ProjectMember::where('project_id', $project->id)
                ->where('status', 'Active')
                ->get();

            if ($activeMembers->isEmpty()) {
                continue;
            }

            // Create 5 main tasks per project
            for ($i = 1; $i <= 5; $i++) {
                $assignee = $activeMembers->random()->user_id;

                $task = Task::factory()->create([
                    'company_id' => $project->company_id,
                    'project_id' => $project->id,
                    'parent_id' => null,
                    'assigned_to' => $assignee,
                    'task_code' => 'TSK-' . $project->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'created_by' => $project->created_by ?? 1,
                    'updated_by' => $project->updated_by ?? 1,
                ]);

                // Adjust progress and completed_at based on status
                if ($task->status === 'Completed') {
                    $task->update([
                        'progress' => 100,
                        'completed_at' => now(),
                    ]);
                }

                // Create 2 subtasks for some tasks
                if (rand(1, 10) > 5) {
                    for ($j = 1; $j <= 2; $j++) {
                        $subAssignee = $activeMembers->random()->user_id;
                        $subTask = Task::factory()->create([
                            'company_id' => $project->company_id,
                            'project_id' => $project->id,
                            'parent_id' => $task->id,
                            'assigned_to' => $subAssignee,
                            'task_code' => 'TSK-' . $project->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.' . $j,
                            'created_by' => $project->created_by ?? 1,
                            'updated_by' => $project->updated_by ?? 1,
                        ]);

                        if ($subTask->status === 'Completed') {
                            $subTask->update([
                                'progress' => 100,
                                'completed_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
