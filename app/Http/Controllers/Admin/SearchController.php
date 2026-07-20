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

        // Search Leads
        $leads = \App\Models\Lead::where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('company_name', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($lead) {
            return [
                'id' => $lead->id,
                'title' => $lead->first_name . ' ' . $lead->last_name,
                'subtitle' => $lead->email . ($lead->company_name ? ' - ' . $lead->company_name : ''),
                'url' => route('admin.crm.leads.show', $lead)
            ];
        })->values();

        // Search Contacts
        $contacts = \App\Models\Contact::where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($contact) {
            return [
                'id' => $contact->id,
                'title' => $contact->first_name . ' ' . $contact->last_name,
                'subtitle' => $contact->email,
                'url' => route('admin.crm.contacts.show', $contact)
            ];
        })->values();

        // Search Accounts
        $accounts = \App\Models\Account::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($account) {
            return [
                'id' => $account->id,
                'title' => $account->name,
                'subtitle' => $account->email ?? 'No email',
                'url' => route('admin.crm.accounts.show', $account)
            ];
        })->values();

        // Search Quotations
        $quotations = \App\Models\Quotation::where(function($q) use ($query) {
            $q->where('quotation_number', 'like', "%{$query}%")
              ->orWhere('reference', 'like', "%{$query}%")
              ->orWhereHas('account', function($sq) use ($query) {
                  $sq->where('name', 'like', "%{$query}%");
              });
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($quotation) {
            return [
                'id' => $quotation->id,
                'title' => $quotation->quotation_number . ($quotation->reference ? ' (' . $quotation->reference . ')' : ''),
                'subtitle' => 'Customer: ' . ($quotation->account->name ?? 'Unknown') . ' | Total: ' . format_currency($quotation->grand_total),
                'url' => route('admin.crm.quotations.show', $quotation)
            ];
        })->values();

        return response()->json([
            'projects' => $projects,
            'tasks' => $tasks,
            'time_entries' => $timeEntries,
            'leads' => $leads,
            'contacts' => $contacts,
            'accounts' => $accounts,
            'quotations' => $quotations,
        ]);
    }
}
