<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Task;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $milestones = Milestone::factory(3)->create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
            ]);
            
            // Assign some tasks from the project to the milestones
            $tasks = Task::where('project_id', $project->id)->get();
            if ($tasks->count() > 0) {
                foreach ($milestones as $milestone) {
                    $milestoneTasks = $tasks->random(min(3, $tasks->count()));
                    // Ensure task is only in one active milestone could be tricky with random,
                    // but for seeding it's acceptable to just attach them.
                    $milestone->tasks()->syncWithoutDetaching($milestoneTasks->pluck('id')->toArray());
                }
            }
        }
    }
}
