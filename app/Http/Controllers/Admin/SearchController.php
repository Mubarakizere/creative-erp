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

        // Search Invoices
        $invoices = \App\Models\Invoice::where(function($q) use ($query) {
            $q->where('invoice_number', 'like', "%{$query}%")
              ->orWhereHas('client', function($sq) use ($query) {
                  $sq->where('company_name', 'like', "%{$query}%")
                     ->orWhere('first_name', 'like', "%{$query}%")
                     ->orWhere('last_name', 'like', "%{$query}%");
              });
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'title' => $invoice->invoice_number,
                'subtitle' => 'Customer: ' . ($invoice->client->name ?? 'Unknown') . ' | Total: ' . format_currency($invoice->total_amount),
                'url' => route('admin.finance.invoices.show', $invoice)
            ];
        })->values();

        // Search Receipts
        $receipts = \App\Models\Receipt::where(function($q) use ($query) {
            $q->where('receipt_number', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($receipt) {
            return [
                'id' => $receipt->id,
                'title' => $receipt->receipt_number,
                'subtitle' => 'Generated on ' . $receipt->generated_at->format('M d, Y'),
                'url' => route('admin.finance.receipts.show', $receipt)
            ];
        })->values();

        // Search Payments
        $payments = \App\Models\Payment::where(function($q) use ($query) {
            $q->where('payment_number', 'like', "%{$query}%")
              ->orWhere('reference_number', 'like', "%{$query}%")
              ->orWhereHas('client', function($sq) use ($query) {
                  $sq->where('company_name', 'like', "%{$query}%")
                     ->orWhere('first_name', 'like', "%{$query}%")
                     ->orWhere('last_name', 'like', "%{$query}%");
              });
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($payment) {
            return [
                'id' => $payment->id,
                'title' => $payment->payment_number . ($payment->reference_number ? ' (' . $payment->reference_number . ')' : ''),
                'subtitle' => 'Customer: ' . ($payment->client->name ?? 'Unknown') . ' | Amount: ' . format_currency($payment->amount),
                'url' => route('admin.finance.payments.show', $payment)
            ];
        })->values();

        // Search Clients
        $clients = \App\Models\Client::where(function($q) use ($query) {
            $q->where('company_name', 'like', "%{$query}%")
              ->orWhere('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($client) {
            return [
                'id' => $client->id,
                'title' => $client->name,
                'subtitle' => $client->email ?? 'No email',
                'url' => route('admin.clients.show', $client)
            ];
        })->values();

        // Search Journals
        $journals = \App\Models\Journal::where(function($q) use ($query) {
            $q->where('journal_number', 'like', "%{$query}%")
              ->orWhere('reference_number', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($journal) {
            return [
                'id' => $journal->id,
                'title' => 'Journal: ' . $journal->journal_number,
                'subtitle' => 'Status: ' . $journal->status . ' | Total: ' . $journal->total_debit,
                'url' => route('admin.finance.accounting.journals.show', $journal)
            ];
        })->values();

        // Search Chart of Accounts
        $chartOfAccounts = \App\Models\ChartOfAccount::where(function($q) use ($query) {
            $q->where('code', 'like', "%{$query}%")
              ->orWhere('name', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($account) {
            return [
                'id' => $account->id,
                'title' => $account->code . ' - ' . $account->name,
                'subtitle' => 'Type: ' . ($account->accountType->name ?? 'Unknown'),
                'url' => route('admin.finance.accounting.chart-of-accounts.show', $account)
            ];
        })->values();

        // Search General Ledger
        $generalLedger = \App\Models\GeneralLedger::where(function($q) use ($query) {
            $q->where('reference', 'like', "%{$query}%");
        })->get()->filter(fn($model) => auth()->user()->can('view', $model))->take(5)->map(function ($ledger) {
            return [
                'id' => $ledger->id,
                'title' => 'Ledger Entry: ' . $ledger->reference,
                'subtitle' => 'Account: ' . ($ledger->chartOfAccount->name ?? 'Unknown') . ' | Dr: ' . $ledger->debit . ' Cr: ' . $ledger->credit,
                'url' => route('admin.finance.accounting.ledger.index') . '?account_id=' . $ledger->chart_of_account_id
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
            'invoices' => $invoices,
            'receipts' => $receipts,
            'payments' => $payments,
            'clients' => $clients,
            'journals' => $journals,
            'chart_of_accounts' => $chartOfAccounts,
            'general_ledger' => $generalLedger,
        ]);
    }
}
