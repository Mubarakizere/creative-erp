<?php

namespace App\Services;

use App\Models\TimeEntry;
use Illuminate\Support\Carbon;

class TimerService
{
    /**
     * Start a new timer for the user.
     */
    public function startTimer(array $data, $user = null)
    {
        $user = $user ?? auth()->user();

        // Check if there is an existing running timer
        if ($this->hasRunningTimer($user->id)) {
            throw new \Exception('You already have a running timer.');
        }

        $project = \App\Models\Project::find($data['project_id']);

        return TimeEntry::create([
            'company_id' => $project->company_id ?? $user->company_id,
            'branch_id' => $project->branch_id ?? $user->branch_id,
            'project_id' => $data['project_id'],
            'task_id' => $data['task_id'] ?? null,
            'user_id' => $user->id,
            'description' => $data['description'] ?? null,
            'start_time' => now(),
            'end_time' => null,
            'duration_minutes' => 0,
            'billable' => $data['billable'] ?? true,
            'status' => 'running',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Stop a running timer.
     */
    public function stopTimer(TimeEntry $entry, $user = null)
    {
        if ($entry->status !== 'running' && $entry->status !== 'paused') {
            throw new \Exception('This timer cannot be stopped.');
        }

        $endTime = now();
        $durationMinutes = 0;
        
        if ($entry->status === 'running') {
            $durationMinutes = $entry->start_time->diffInMinutes($endTime);
        }
        
        $totalDuration = $entry->duration_minutes + $durationMinutes;

        $entry->update([
            'end_time' => $endTime,
            'duration_minutes' => $totalDuration,
            'status' => 'completed',
        ]);

        return $entry;
    }

    /**
     * Pause a running timer.
     */
    public function pauseTimer(TimeEntry $entry, $user = null)
    {
        if ($entry->status !== 'running') {
            throw new \Exception('Only a running timer can be paused.');
        }

        $now = now();
        $durationMinutes = $entry->start_time->diffInMinutes($now);
        
        $entry->update([
            'duration_minutes' => $entry->duration_minutes + $durationMinutes,
            'status' => 'paused',
        ]);

        return $entry;
    }

    /**
     * Resume a paused timer.
     */
    public function resumeTimer(TimeEntry $entry, $user = null)
    {
        if ($entry->status !== 'paused') {
            throw new \Exception('Only a paused timer can be resumed.');
        }

        $user = $user ?? auth()->user();

        // Ensure no other running timer
        if ($this->hasRunningTimer($user->id)) {
            throw new \Exception('You already have a running timer.');
        }

        $entry->update([
            'start_time' => now(), // reset start time to now for new session computation
            'status' => 'running',
        ]);

        return $entry;
    }

    public function hasRunningTimer($userId): bool
    {
        return TimeEntry::where('user_id', $userId)
            ->where('status', 'running')
            ->exists();
    }

    public function getRunningTimer($userId): ?TimeEntry
    {
        return TimeEntry::with(['project', 'task'])->where('user_id', $userId)
            ->where('status', 'running')
            ->first();
    }
}
