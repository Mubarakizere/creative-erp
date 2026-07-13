<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectTeamService
{
    /**
     * Assign a new member to a project.
     *
     * @param Project $project
     * @param array $data
     * @return ProjectMember
     * @throws ValidationException
     */
    public function assignMember(Project $project, array $data): ProjectMember
    {
        $this->validateAssignment($project, $data);

        return DB::transaction(function () use ($project, $data) {
            $member = $project->projectMembers()->create([
                'user_id' => $data['user_id'],
                'department_id' => $data['department_id'],
                'project_role' => $data['project_role'],
                'allocation_percentage' => $data['allocation_percentage'] ?? 100,
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'joined_at' => $data['joined_at'],
                'status' => $data['status'] ?? 'Active',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->handleProjectManagerRole($project, $member);

            return $member;
        });
    }

    /**
     * Update an existing member assignment.
     *
     * @param ProjectMember $member
     * @param array $data
     * @return ProjectMember
     * @throws ValidationException
     */
    public function updateAssignment(ProjectMember $member, array $data): ProjectMember
    {
        $this->validateAssignment($member->project, $data, $member->id);

        return DB::transaction(function () use ($member, $data) {
            $member->update([
                'department_id' => $data['department_id'] ?? $member->department_id,
                'project_role' => $data['project_role'] ?? $member->project_role,
                'allocation_percentage' => $data['allocation_percentage'] ?? $member->allocation_percentage,
                'hourly_rate' => array_key_exists('hourly_rate', $data) ? $data['hourly_rate'] : $member->hourly_rate,
                'joined_at' => $data['joined_at'] ?? $member->joined_at,
                'left_at' => array_key_exists('left_at', $data) ? $data['left_at'] : $member->left_at,
                'status' => $data['status'] ?? $member->status,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $member->notes,
                'updated_by' => auth()->id(),
            ]);

            $this->handleProjectManagerRole($member->project, $member);

            return $member;
        });
    }

    /**
     * Remove a member from the project team (soft delete).
     *
     * @param ProjectMember $member
     * @return bool
     */
    public function removeMember(ProjectMember $member): bool
    {
        $member->update([
            'left_at' => now(),
            'updated_by' => auth()->id(),
        ]);
        
        return $member->delete();
    }

    /**
     * Restore a previously removed member.
     *
     * @param ProjectMember $member
     * @return bool
     */
    public function restoreMember(ProjectMember $member): bool
    {
        $restored = $member->restore();
        
        if ($restored) {
            $member->update([
                'left_at' => null,
                'updated_by' => auth()->id(),
            ]);
        }
        
        return $restored;
    }

    /**
     * Activate a team member.
     *
     * @param ProjectMember $member
     * @return bool
     */
    public function activateMember(ProjectMember $member): bool
    {
        // If activating as Project Manager, check rules
        if ($member->project_role === 'Project Manager') {
            $this->enforceSingleProjectManager($member->project, $member->id);
        }

        return $member->update([
            'status' => 'Active',
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Deactivate a team member.
     *
     * @param ProjectMember $member
     * @return bool
     */
    public function deactivateMember(ProjectMember $member): bool
    {
        return $member->update([
            'status' => 'Inactive',
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Validate the assignment rules.
     *
     * @param Project $project
     * @param array $data
     * @param int|null $excludeMemberId
     * @throws ValidationException
     */
    protected function validateAssignment(Project $project, array $data, ?int $excludeMemberId = null): void
    {
        // 1. Prevent duplicate assignments (a user cannot be assigned twice to the same project)
        if (isset($data['user_id'])) {
            $existing = $project->projectMembers()
                ->where('user_id', $data['user_id'])
                ->when($excludeMemberId, fn($q) => $q->where('id', '!=', $excludeMemberId))
                ->withTrashed()
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'user_id' => 'This user is already assigned to the project.',
                ]);
            }
        }

        // 2. Validate Allocation Percentage
        $allocation = $data['allocation_percentage'] ?? null;
        if ($allocation !== null && ($allocation < 1 || $allocation > 100)) {
            throw ValidationException::withMessages([
                'allocation_percentage' => 'Allocation percentage must be between 1 and 100.',
            ]);
        }
        
        // 3. Prevent multiple active project managers if role or status is changing to PM / Active
        $role = $data['project_role'] ?? null;
        $status = $data['status'] ?? null;
        
        if ($role === 'Project Manager' && $status !== 'Inactive') {
            $this->enforceSingleProjectManager($project, $excludeMemberId);
        }
    }

    /**
     * Ensures only one active project manager exists for the project.
     *
     * @param Project $project
     * @param int|null $excludeMemberId
     * @throws ValidationException
     */
    protected function enforceSingleProjectManager(Project $project, ?int $excludeMemberId = null): void
    {
        $existingManager = $project->projectMembers()
            ->where('project_role', 'Project Manager')
            ->where('status', 'Active')
            ->when($excludeMemberId, fn($q) => $q->where('id', '!=', $excludeMemberId))
            ->first();

        if ($existingManager) {
            throw ValidationException::withMessages([
                'project_role' => 'This project already has an active Project Manager ('.$existingManager->user->full_name.'). Only one active Project Manager is allowed.',
            ]);
        }
    }

    /**
     * Handle updating the main Project's project_manager_id if the member is a Project Manager.
     *
     * @param Project $project
     * @param ProjectMember $member
     */
    protected function handleProjectManagerRole(Project $project, ProjectMember $member): void
    {
        if ($member->project_role === 'Project Manager' && $member->status === 'Active') {
            if ($project->project_manager_id !== $member->user_id) {
                $project->update([
                    'project_manager_id' => $member->user_id
                ]);
            }
        } elseif ($member->project_role === 'Project Manager' && $member->status === 'Inactive') {
            // If the manager was deactivated, clear the project manager id
            if ($project->project_manager_id === $member->user_id) {
                $project->update([
                    'project_manager_id' => null
                ]);
            }
        }
    }
}
