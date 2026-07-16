<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::with('tasks')->get();
        
        if ($users->isEmpty() || $projects->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Generate completed time entries
            for ($i = 0; $i < 20; $i++) {
                $project = $projects->random();
                $task = $project->tasks->count() > 0 ? $project->tasks->random() : null;
                
                $daysAgo = rand(0, 30);
                $startTime = now()->subDays($daysAgo)->setHour(rand(8, 16))->setMinute(rand(0, 59));
                $durationMinutes = rand(30, 240);
                $endTime = (clone $startTime)->addMinutes($durationMinutes);

                TimeEntry::factory()->create([
                    'company_id' => $project->company_id,
                    'branch_id' => $project->branch_id,
                    'project_id' => $project->id,
                    'task_id' => $task ? $task->id : null,
                    'user_id' => $user->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_minutes' => $durationMinutes,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
            
            // Randomly assign a running timer to some users (e.g. 20% of users)
            if (rand(1, 100) <= 20) {
                $project = $projects->random();
                $task = $project->tasks->count() > 0 ? $project->tasks->random() : null;
                
                TimeEntry::factory()->running()->create([
                    'company_id' => $project->company_id,
                    'branch_id' => $project->branch_id,
                    'project_id' => $project->id,
                    'task_id' => $task ? $task->id : null,
                    'user_id' => $user->id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }
}
