<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Exception;

class MilestoneService
{
    /**
     * Create a new milestone
     */
    public function createMilestone(array $data): Milestone
    {
        return DB::transaction(function () use ($data) {
            if (($data['status'] ?? 'Pending') === 'Completed') {
                $data['progress'] = 100;
                $data['completed_at'] = now();
            }

            return Milestone::create($data);
        });
    }

    /**
     * Update an existing milestone
     */
    public function updateMilestone(Milestone $milestone, array $data): Milestone
    {
        return DB::transaction(function () use ($milestone, $data) {
            if (isset($data['status']) && $data['status'] === 'Completed' && $milestone->status !== 'Completed') {
                $data['progress'] = 100;
                $data['completed_at'] = now();
            } elseif (isset($data['status']) && $data['status'] !== 'Completed' && $milestone->status === 'Completed') {
                $data['completed_at'] = null;
            }

            $milestone->update($data);

            return $milestone;
        });
    }

    /**
     * Delete a milestone
     */
    public function deleteMilestone(Milestone $milestone): bool
    {
        return $milestone->delete();
    }

    /**
     * Restore a deleted milestone
     */
    public function restoreMilestone(Milestone $milestone): bool
    {
        return $milestone->restore();
    }

    /**
     * Duplicate a milestone
     */
    public function duplicateMilestone(Milestone $milestone, array $overrides = []): Milestone
    {
        $newData = array_merge($milestone->toArray(), $overrides);
        
        unset($newData['id'], $newData['uuid'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['completed_at']);
        
        $newData['status'] = 'Pending';
        $newData['progress'] = 0;
        
        if (!isset($overrides['name'])) {
            $newData['name'] = $milestone->name . ' (Copy)';
        }

        return $this->createMilestone($newData);
    }

    /**
     * Assign tasks to a milestone
     */
    public function assignTasks(Milestone $milestone, array $taskIds): void
    {
        DB::transaction(function () use ($milestone, $taskIds) {
            foreach ($taskIds as $taskId) {
                $task = Task::findOrFail($taskId);

                // Prevent assigning tasks from another project
                if ($task->project_id !== $milestone->project_id) {
                    throw new Exception("Task {$task->task_code} belongs to a different project.");
                }

                // Prevent assigning archived tasks
                if ($task->trashed()) {
                    throw new Exception("Cannot assign archived task {$task->task_code}.");
                }

                // Prevent assigning a task to multiple active milestones
                $activeMilestonesCount = $task->milestones()
                    ->where('milestones.status', '!=', 'Completed')
                    ->where('milestones.id', '!=', $milestone->id)
                    ->count();

                if ($activeMilestonesCount > 0) {
                    throw new Exception("Task {$task->task_code} is already assigned to another active milestone.");
                }

                $milestone->tasks()->syncWithoutDetaching([$taskId]);
            }

            $this->calculateProgress($milestone);
        });
    }

    /**
     * Remove task from milestone
     */
    public function removeTask(Milestone $milestone, int $taskId): void
    {
        $milestone->tasks()->detach($taskId);
        $this->calculateProgress($milestone);
    }

    /**
     * Calculate milestone progress automatically
     */
    public function calculateProgress(Milestone $milestone): void
    {
        $totalTasks = $milestone->tasks()->count();

        if ($totalTasks === 0) {
            $milestone->update([
                'progress' => 0,
                'status' => 'Pending',
                'completed_at' => null
            ]);
            return;
        }

        $completedTasks = $milestone->tasks()->where('status', 'Completed')->count();
        $progress = (int) round(($completedTasks / $totalTasks) * 100);

        $updateData = ['progress' => $progress];

        if ($progress === 100) {
            $updateData['status'] = 'Completed';
            $updateData['completed_at'] = now();
        } else {
            // If it was completed but a task was reopened (or new task added)
            if ($milestone->status === 'Completed') {
                $updateData['status'] = 'In Progress';
                $updateData['completed_at'] = null;
            } elseif ($milestone->status === 'Pending' && $progress > 0) {
                $updateData['status'] = 'In Progress';
            }
        }

        $milestone->update($updateData);
    }
}
