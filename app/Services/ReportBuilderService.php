<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Approval;
use App\Models\Document;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Client;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Metrics\ReportMetrics;
use Illuminate\Database\Eloquent\Builder;

use App\Services\Finance\FinancialStatementService;
use App\Services\Finance\AccountingReportService;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralLedger;

class ReportBuilderService
{
    protected ReportMetrics $reportMetrics;
    protected FinancialStatementService $financialService;
    protected AccountingReportService $accountingService;

    public function __construct(
        ReportMetrics $reportMetrics,
        FinancialStatementService $financialService,
        AccountingReportService $accountingService
    ) {
        $this->reportMetrics = $reportMetrics;
        $this->financialService = $financialService;
        $this->accountingService = $accountingService;
    }

    /**
     * Build the dataset for the report table based on type and filters.
     */
    public function build(string $type, array $filters = [])
    {
        return match ($type) {
            'executive' => $this->buildExecutiveSummary($filters),
            'project_summary' => $this->buildProjectSummary($filters),
            'task_summary' => $this->buildTaskSummary($filters),
            'time_summary' => $this->buildTimeSummary($filters),
            'user_productivity' => $this->buildUserProductivity($filters),
            'meetings' => $this->buildMeetingsSummary($filters),
            'workflow' => $this->buildWorkflowSummary($filters),
            'documents' => $this->buildDocumentsSummary($filters),
            'discussions' => $this->buildDiscussionsSummary($filters),
            'organizations' => $this->buildOrganizationsSummary($filters),
            'clients' => $this->buildClientsSummary($filters),
            'announcements' => $this->buildAnnouncementsSummary($filters),
            'notifications' => $this->buildNotificationsSummary($filters),
            'crm_pipeline' => $this->buildCrmPipeline($filters),
            'crm_leads' => $this->buildCrmLeads($filters),
            'crm_conversions' => $this->buildCrmConversions($filters),
            'quotation_summary' => $this->buildQuotationSummary($filters),
            'sales_forecast' => $this->buildSalesForecast($filters),
            'approval_summary' => $this->buildWorkflowSummary($filters),
            'invoice_summary' => $this->buildInvoiceSummary($filters),
            'payment_summary' => $this->buildPaymentSummary($filters),
            'aging_report' => $this->buildAgingReport($filters),
            'revenue_report' => $this->buildRevenueReport($filters),
            'customer_statements' => $this->buildCustomerStatements($filters),
            'profit_and_loss' => $this->buildProfitAndLoss($filters),
            'balance_sheet' => $this->buildBalanceSheet($filters),
            'cash_flow' => $this->buildCashFlow($filters),
            'expense_analysis' => $this->buildExpenseAnalysis($filters),
            'budget_analysis' => $this->buildBudgetAnalysis($filters),
            'customer_profitability' => $this->buildCustomerProfitability($filters),
            'project_profitability' => $this->buildProjectProfitability($filters),
            'inventory_valuation' => $this->buildInventoryValuation($filters),
            'stock_on_hand' => $this->buildStockOnHand($filters),
            'low_stock' => $this->buildLowStock($filters),
            'warehouse_summary' => $this->buildWarehouseSummary($filters),
            'inventory_transactions' => $this->buildInventoryTransactions($filters),
            'inventory_adjustments' => $this->buildInventoryAdjustments($filters),
            default => collect([]),
        };
    }

    protected function buildExecutiveSummary(array $filters)
    {
        // Executive report is a compilation of all module stats
        $summaries = $this->reportMetrics->getReportSummaries($filters);
        return collect([$summaries]);
    }

    protected function buildProjectSummary(array $filters)
    {
        $query = Project::query()->with(['client', 'manager']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->whereIn('priority', (array) $filters['priority']);
        }
        if (!empty($filters['client_id'])) {
            $query->whereIn('client_id', (array) $filters['client_id']);
        }
        if (!empty($filters['manager_id'])) {
            $query->whereIn('manager_id', (array) $filters['manager_id']);
        }

        return $query->get();
    }

    protected function buildTaskSummary(array $filters)
    {
        $query = Task::query()->with(['project']);
        
        if (!empty($filters['project_id'])) {
            $query->whereIn('project_id', (array) $filters['project_id']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->whereIn('assigned_to', (array) $filters['assigned_to']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->whereIn('priority', (array) $filters['priority']);
        }
        
        $this->applyDateFilters($query, $filters, 'due_date', 'date_from', 'date_to');

        return $query->get();
    }

    protected function buildTimeSummary(array $filters)
    {
        $query = TimeEntry::query()->with(['user', 'task', 'project']);
        
        if (!empty($filters['user_id'])) {
            $query->whereIn('user_id', (array) $filters['user_id']);
        }
        if (!empty($filters['project_id'])) {
            $query->whereIn('project_id', (array) $filters['project_id']);
        }
        if (isset($filters['is_billable']) && $filters['is_billable'] !== '') {
            $query->where('is_billable', $filters['is_billable']);
        }

        $this->applyDateFilters($query, $filters, 'start_time');

        return $query->get();
    }

    protected function buildUserProductivity(array $filters)
    {
        $query = User::query()->withCount('assignedTasks')->withSum('timeEntries', 'duration_minutes');
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        return $query->get();
    }

    protected function buildMeetingsSummary(array $filters)
    {
        $query = Meeting::query()->with(['organizer']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['organizer_id'])) {
            $query->whereIn('organizer_id', (array) $filters['organizer_id']);
        }
        if (!empty($filters['type'])) {
            $query->whereIn('type', (array) $filters['type']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        $this->applyDateFilters($query, $filters, 'start_time');

        return $query->get();
    }

    protected function buildWorkflowSummary(array $filters)
    {
        $query = Approval::query()->with(['workflow', 'requester', 'approver']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['workflow_id'])) {
            $query->whereIn('workflow_id', (array) $filters['workflow_id']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        return $query->get();
    }

    protected function buildDocumentsSummary(array $filters)
    {
        $query = Document::query()->with(['uploader', 'category']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['category_id'])) {
            $query->whereIn('category_id', (array) $filters['category_id']);
        }

        return $query->get();
    }

    protected function buildDiscussionsSummary(array $filters)
    {
        $query = Comment::query()->with(['user', 'commentable']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['user_id'])) {
            $query->whereIn('user_id', (array) $filters['user_id']);
        }

        return $query->get();
    }

    protected function buildOrganizationsSummary(array $filters)
    {
        $query = Company::query()->withCount(['branches', 'departments', 'users']);
        // Organizations don't usually filter by themselves via common filters
        return $query->get();
    }

    protected function buildClientsSummary(array $filters)
    {
        $query = Client::query()->withCount(['projects']);
        $this->applyCommonFilters($query, $filters);

        return $query->get();
    }

    protected function buildAnnouncementsSummary(array $filters)
    {
        $query = Announcement::query()->with(['creator']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['priority'])) {
            $query->whereIn('priority', (array) $filters['priority']);
        }

        return $query->get();
    }

    protected function buildNotificationsSummary(array $filters)
    {
        $query = Notification::query()->with(['user']);
        
        if (!empty($filters['user_id'])) {
            $query->whereIn('user_id', (array) $filters['user_id']);
        }
        
        $this->applyDateFilters($query, $filters, 'created_at');

        return $query->get();
    }

    protected function buildCrmPipeline(array $filters)
    {
        $query = \App\Models\Opportunity::query()->with(['pipeline', 'stage', 'owner']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        $this->applyDateFilters($query, $filters, 'created_at');

        return $query->get();
    }

    protected function buildCrmLeads(array $filters)
    {
        $query = \App\Models\Lead::query()->with(['owner']);
        $this->applyCommonFilters($query, $filters);

        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }

        $this->applyDateFilters($query, $filters, 'created_at');

        return $query->get();
    }

    protected function buildCrmConversions(array $filters)
    {
        $query = \App\Models\Lead::query()->whereNotNull('converted_at')->with(['convertedOpportunity']);
        $this->applyCommonFilters($query, $filters);

        $this->applyDateFilters($query, $filters, 'converted_at');

        return $query->get();
    }

    protected function buildQuotationSummary(array $filters)
    {
        $query = \App\Models\Quotation::query()->with(['account', 'status', 'owner']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereHas('status', function($q) use ($filters) {
                $q->whereIn('name', (array) $filters['status']);
            });
        }
        
        $this->applyDateFilters($query, $filters, 'created_at');

        return $query->get();
    }

    protected function buildSalesForecast(array $filters)
    {
        $query = \App\Models\Opportunity::query()->with(['account', 'owner', 'stage']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        
        $this->applyDateFilters($query, $filters, 'expected_close_date');

        return $query->get();
    }

    protected function buildInvoiceSummary(array $filters)
    {
        $query = Invoice::query()->with(['client', 'project']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        if (!empty($filters['client_id'])) {
            $query->whereIn('client_id', (array) $filters['client_id']);
        }
        
        $this->applyDateFilters($query, $filters, 'issue_date');

        return $query->get();
    }

    protected function buildPaymentSummary(array $filters)
    {
        $query = Payment::query()->with(['client', 'paymentMethod']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        if (!empty($filters['client_id'])) {
            $query->whereIn('client_id', (array) $filters['client_id']);
        }
        
        $this->applyDateFilters($query, $filters, 'payment_date');

        return $query->get();
    }

    protected function buildAgingReport(array $filters)
    {
        $query = Invoice::query()->with(['client'])->where('status', 'Overdue');
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['client_id'])) {
            $query->whereIn('client_id', (array) $filters['client_id']);
        }
        
        $invoices = $query->get();
        // Decorate with aging buckets for easy table display
        $invoices->each(function ($invoice) {
            $days = $invoice->due_date ? $invoice->due_date->diffInDays(now(), false) : 0;
            $invoice->aging_days = max(0, $days);
            
            if ($invoice->aging_days <= 30) {
                $invoice->aging_bucket = '1-30 Days';
            } elseif ($invoice->aging_days <= 60) {
                $invoice->aging_bucket = '31-60 Days';
            } elseif ($invoice->aging_days <= 90) {
                $invoice->aging_bucket = '61-90 Days';
            } else {
                $invoice->aging_bucket = '90+ Days';
            }
        });
        
        return $invoices;
    }

    protected function buildRevenueReport(array $filters)
    {
        $query = Payment::query()->with(['client'])->where('status', 'Completed');
        $this->applyCommonFilters($query, $filters);
        
        $this->applyDateFilters($query, $filters, 'payment_date');
        
        // Return raw payments, which can be grouped by month in the view or export
        return $query->get();
    }

    protected function buildCustomerStatements(array $filters)
    {
        $query = Client::query()->with(['invoices', 'payments']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['client_id'])) {
            $query->whereIn('id', (array) $filters['client_id']);
        }
        
        return $query->get();
    }

    protected function applyCommonFilters(Builder $query, array $filters, string $relation = null)
    {
        $prefix = $relation ? $relation . '.' : '';

        // Safely apply filters only if the columns exist or are expected.
        // Assuming most models use these standard multi-tenant columns.
        if (!empty($filters['company_id'])) {
            $query->whereIn($prefix . 'company_id', (array) $filters['company_id']);
        }
        if (!empty($filters['branch_id'])) {
            $query->whereIn($prefix . 'branch_id', (array) $filters['branch_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereIn($prefix . 'department_id', (array) $filters['department_id']);
        }

        $this->applyDateFilters($query, $filters, $prefix . 'created_at');
    }

    protected function applyDateFilters(Builder $query, array $filters, string $column, string $fromKey = 'date_from', string $toKey = 'date_to')
    {
        if (!empty($filters[$fromKey])) {
            $query->whereDate($column, '>=', $filters[$fromKey]);
        }
        if (!empty($filters[$toKey])) {
            $query->whereDate($column, '<=', $filters[$toKey]);
        }
    }

    protected function buildProfitAndLoss(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        $data = $this->financialService->generateProfitAndLoss(
            $companyId, 
            $filters['date_from'] ?? null, 
            $filters['date_to'] ?? null, 
            $filters
        );
        return collect([$data]); // Wrap in collection
    }

    protected function buildBalanceSheet(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        $data = $this->financialService->generateBalanceSheet(
            $companyId, 
            $filters['date_to'] ?? null, 
            $filters
        );
        return collect([$data]);
    }

    protected function buildCashFlow(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        $data = $this->financialService->generateCashFlowStatement(
            $companyId, 
            $filters['date_from'] ?? null, 
            $filters['date_to'] ?? null, 
            $filters
        );
        return collect([$data]);
    }

    protected function buildExpenseAnalysis(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        $query = GeneralLedger::with(['chartOfAccount.accountType', 'department', 'branch'])
            ->where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('category', 'Expense');
            });

        if (!empty($filters['branch_id'])) {
            $query->whereIn('branch_id', (array) $filters['branch_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereIn('department_id', (array) $filters['department_id']);
        }
        $this->applyDateFilters($query, $filters, 'date');

        // Group by account to get totals
        $expenses = $query->select(
            'chart_of_account_id',
            'department_id',
            'branch_id',
            DB::raw('SUM(debit) as total_expense')
        )
        ->groupBy('chart_of_account_id', 'department_id', 'branch_id')
        ->orderByDesc('total_expense')
        ->get();

        return collect([
            'expenses' => $expenses,
            'total' => $expenses->sum('total_expense')
        ]);
    }

    protected function buildBudgetAnalysis(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        $query = \App\Models\Budget::with(['department', 'branch'])
            ->where('company_id', $companyId);

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', $filters['fiscal_year_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereIn('department_id', (array) $filters['department_id']);
        }

        $budgets = $query->get();
        // Decorate with actuals (this requires querying GeneralLedger based on budget's dimension)
        $budgets->each(function($budget) {
            $actual = GeneralLedger::where('company_id', $budget->company_id)
                ->where('fiscal_year_id', $budget->fiscal_year_id)
                ->where('department_id', $budget->department_id)
                ->whereHas('chartOfAccount.accountType', function($q) {
                    $q->where('category', 'Expense');
                })->sum('debit');

            $budget->actual_amount = $actual;
            $budget->variance = $budget->amount - $actual;
            $budget->variance_percentage = $budget->amount > 0 ? ($budget->variance / $budget->amount) * 100 : 0;
        });

        return $budgets;
    }

    protected function buildCustomerProfitability(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        
        $query = Client::where('company_id', $companyId);
        if (!empty($filters['client_id'])) {
            $query->whereIn('id', (array) $filters['client_id']);
        }

        $clients = $query->get();

        // For each client, calculate revenue and expenses associated with their client_id in GL
        $clients->each(function($client) use ($filters) {
            $glQuery = GeneralLedger::where('client_id', $client->id)
                ->join('chart_of_accounts', 'general_ledgers.chart_of_account_id', '=', 'chart_of_accounts.id')
                ->join('account_types', 'chart_of_accounts.account_type_id', '=', 'account_types.id')
                ->select(
                    DB::raw("SUM(CASE WHEN account_types.category = 'Revenue' THEN general_ledgers.credit - general_ledgers.debit ELSE 0 END) as total_revenue"),
                    DB::raw("SUM(CASE WHEN account_types.category = 'Expense' THEN general_ledgers.debit - general_ledgers.credit ELSE 0 END) as total_expense")
                );

            $this->applyDateFilters($glQuery, $filters, 'date');
            
            $totals = $glQuery->first();
            $client->total_revenue = $totals->total_revenue ?? 0;
            $client->total_expense = $totals->total_expense ?? 0;
            $client->profit = $client->total_revenue - $client->total_expense;
            $client->profit_margin = $client->total_revenue > 0 ? ($client->profit / $client->total_revenue) * 100 : 0;
        });

        return $clients->sortByDesc('profit')->values();
    }

    protected function buildProjectProfitability(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()->company_id ?? 1;
        
        $query = Project::with('client')->where('company_id', $companyId);
        if (!empty($filters['project_id'])) {
            $query->whereIn('id', (array) $filters['project_id']);
        }

        $projects = $query->get();

        $projects->each(function($project) use ($filters) {
            $glQuery = GeneralLedger::where('project_id', $project->id)
                ->join('chart_of_accounts', 'general_ledgers.chart_of_account_id', '=', 'chart_of_accounts.id')
                ->join('account_types', 'chart_of_accounts.account_type_id', '=', 'account_types.id')
                ->select(
                    DB::raw("SUM(CASE WHEN account_types.category = 'Revenue' THEN general_ledgers.credit - general_ledgers.debit ELSE 0 END) as total_revenue"),
                    DB::raw("SUM(CASE WHEN account_types.category = 'Expense' THEN general_ledgers.debit - general_ledgers.credit ELSE 0 END) as total_expense")
                );

            $this->applyDateFilters($glQuery, $filters, 'date');
            
            $totals = $glQuery->first();
            $project->total_revenue = $totals->total_revenue ?? 0;
            $project->total_expense = $totals->total_expense ?? 0;
            $project->profit = $project->total_revenue - $project->total_expense;
            $project->profit_margin = $project->total_revenue > 0 ? ($project->profit / $project->total_revenue) * 100 : 0;
        });

        return $projects->sortByDesc('profit')->values();
    }

    protected function buildInventoryValuation(array $filters)
    {
        $query = \App\Models\InventoryValuation::with(['product', 'warehouse']);
        $this->applyCommonFilters($query, $filters, 'products'); // Needs join if filtering by company_id, or just use warehouse
        return $query->get();
    }

    protected function buildStockOnHand(array $filters)
    {
        $query = \App\Models\Inventory::with(['product', 'warehouse', 'zone']);
        $this->applyCommonFilters($query, $filters);
        return $query->get();
    }

    protected function buildLowStock(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()?->company_id;
        
        $query = \App\Models\Product::whereHas('inventory', function ($q) {
            $q->whereColumn('available_quantity', '<=', 'products.minimum_stock');
        })->with('inventory.warehouse');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        return $query->get();
    }

    protected function buildWarehouseSummary(array $filters)
    {
        $query = \App\Models\Warehouse::withCount('inventories')->withSum('inventories', 'available_quantity');
        return $query->get();
    }

    protected function buildInventoryTransactions(array $filters)
    {
        $query = \App\Models\InventoryTransaction::with(['inventory.product', 'inventory.warehouse', 'user', 'reference']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'date');
        return $query->get();
    }

    protected function buildInventoryAdjustments(array $filters)
    {
        $query = \App\Models\InventoryAdjustment::with(['warehouse', 'approvedBy']);
        // If company_id was added to adjustments, we can filter
        return $query->get();
    }
}
