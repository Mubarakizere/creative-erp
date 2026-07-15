<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\DB;
use Exception;

class TaskService
{
    /**
     * Create a new task
     */
    public function createTask(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $this->validateAssignment($data['project_id'], $data['assigned_to'] ?? null);
            $this->validateParentDependency($data['parent_id'] ?? null, null);

            if (($data['status'] ?? 'Pending') === 'Completed') {
                $data['progress'] = 100;
                $data['completed_at'] = now();
            }

            return Task::create($data);
        });
    }

    /**
     * Update an existing task
     */
    public function updateTask(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            if (isset($data['assigned_to']) && $data['assigned_to'] != $task->assigned_to) {
                $this->validateAssignment($task->project_id, $data['assigned_to']);
            }

            if (array_key_exists('parent_id', $data) && $data['parent_id'] != $task->parent_id) {
                $this->validateParentDependency($data['parent_id'], $task->id);
            }

            if (isset($data['status']) && $data['status'] === 'Completed' && $task->status !== 'Completed') {
                $data['progress'] = 100;
                $data['completed_at'] = now();
            } elseif (isset($data['status']) && $data['status'] !== 'Completed' && $task->status === 'Completed') {
                $data['completed_at'] = null;
            }

            // If progress is 100, auto-complete
            if (isset($data['progress']) && $data['progress'] == 100 && ($data['status'] ?? $task->status) !== 'Completed') {
                $data['status'] = 'Completed';
                $data['completed_at'] = now();
            }

            $task->update($data);

            // Recalculate milestone progress if task status changed
            if (isset($data['status'])) {
                foreach ($task->milestones as $milestone) {
                    app(MilestoneService::class)->calculateProgress($milestone);
                }
            }

            return $task;
        });
    }

    /**
     * Delete a task
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Restore a deleted task
     */
    public function restoreTask(Task $task): bool
    {
        return $task->restore();
    }

    /**
     * Duplicate a task
     */
    public function duplicateTask(Task $task, array $overrides = []): Task
    {
        $newData = array_merge($task->toArray(), $overrides);
        
        // Remove ids
        unset($newData['id'], $newData['uuid'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at'], $newData['completed_at']);
        
        $newData['status'] = 'Pending';
        $newData['progress'] = 0;
        
        // Ensure new task_code is unique if not provided
        if (!isset($overrides['task_code'])) {
            $newData['task_code'] = $this->generateUniqueTaskCode($task->project_id, $task->task_code . '-COPY');
        }

        return $this->createTask($newData);
    }

    /**
     * Validates that the assignee is an active project member
     */
    public function validateAssignment($projectId, $userId)
    {
        if (!$userId) {
            return;
        }

        $isActiveMember = ProjectMember::where('project_id', $projectId)
            ->where('user_id', $userId)
            ->where('status', 'Active')
            ->exists();

        if (!$isActiveMember) {
            throw new Exception("The assigned user is not an active member of this project.");
        }
    }

    /**
     * Validates parent dependency to prevent circular references
     */
    public function validateParentDependency($parentId, $taskId)
    {
        if (!$parentId) {
            return;
        }

        if ($parentId == $taskId) {
            throw new Exception("A task cannot be its own parent.");
        }

        if ($taskId) {
            // Check if the parent is actually a descendant of the task
            $parentTask = Task::find($parentId);
            $currentParentId = $parentTask->parent_id;

            while ($currentParentId != null) {
                if ($currentParentId == $taskId) {
                    throw new Exception("Circular dependency detected. A task cannot have one of its descendants as a parent.");
                }
                $ancestor = Task::find($currentParentId);
                $currentParentId = $ancestor ? $ancestor->parent_id : null;
            }
        }
    }
    
    /**
     * Helper to generate a unique task code
     */
    public function generateUniqueTaskCode($projectId, $baseCode)
    {
        $code = $baseCode;
        $counter = 1;
        while (Task::where('project_id', $projectId)->where('task_code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }
        return $code;
    }
}
