<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['projects' => [], 'tasks' => [], 'time_entries' => []]);
        }

        // Search Projects
        $projects = Project::where('name', 'like', "%{$query}%")
            ->orWhere('project_code', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->name,
                    'subtitle' => $project->project_code,
                    'url' => route('admin.projects.show', $project)
                ];
            });

        // Search Tasks
        $tasks = Task::with('project')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('task_code', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->name,
                    'subtitle' => $task->project ? $task->project->name : '',
                    'url' => route('admin.projects.tasks.show', $task)
                ];
            });

        // Search Time Entries
        $timeEntries = TimeEntry::with(['project', 'task'])
            ->where('description', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function ($entry) {
                $durationStr = intdiv($entry->duration_minutes, 60) . 'h ' . ($entry->duration_minutes % 60) . 'm';
                return [
                    'id' => $entry->id,
                    'title' => $entry->description ?? 'Time Log',
                    'subtitle' => ($entry->task ? $entry->task->name : ($entry->project ? $entry->project->name : '')) . ' - ' . $durationStr,
                    'url' => route('admin.time-tracking.timesheet')
                ];
            });

        return response()->json([
            'projects' => $projects,
            'tasks' => $tasks,
            'time_entries' => $timeEntries,
        ]);
    }
}
