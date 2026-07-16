<?php

namespace App\Services;

use App\Models\TimeEntry;
use Illuminate\Support\Carbon;

class TimeTrackingService
{
    /**
     * Create a manual time entry
     */
    public function createEntry(array $data, $user = null)
    {
        $user = $user ?? auth()->user();
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        $durationMinutes = $startTime->diffInMinutes($endTime);

        $this->ensureNoOverlap($user->id, $startTime, $endTime);

        $project = \App\Models\Project::find($data['project_id']);

        return TimeEntry::create([
            'company_id' => $project->company_id ?? $user->company_id,
            'branch_id' => $project->branch_id ?? $user->branch_id,
            'project_id' => $data['project_id'],
            'task_id' => $data['task_id'] ?? null,
            'user_id' => $user->id,
            'description' => $data['description'] ?? null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
            'billable' => $data['billable'] ?? true,
            'status' => 'completed',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Update a time entry
     */
    public function updateEntry(TimeEntry $entry, array $data, $user = null)
    {
        $user = $user ?? auth()->user();
        
        $startTime = isset($data['start_time']) ? Carbon::parse($data['start_time']) : $entry->start_time;
        $endTime = isset($data['end_time']) ? Carbon::parse($data['end_time']) : $entry->end_time;
        
        if ($startTime && $endTime) {
            $durationMinutes = $startTime->diffInMinutes($endTime);
            $this->ensureNoOverlap($entry->user_id, $startTime, $endTime, $entry->id);
            $data['duration_minutes'] = $durationMinutes;
            $data['start_time'] = $startTime;
            $data['end_time'] = $endTime;
        }

        $data['updated_by'] = $user->id;
        $entry->update($data);
        
        return $entry;
    }

    /**
     * Ensure time doesn't overlap with existing completed entries.
     */
    public function ensureNoOverlap($userId, $startTime, $endTime, $excludeId = null)
    {
        $query = TimeEntry::where('user_id', $userId)
            ->where('status', 'completed')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q1) use ($startTime, $endTime) {
                    $q1->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception('Time entry overlaps with an existing record.');
        }
    }
}
