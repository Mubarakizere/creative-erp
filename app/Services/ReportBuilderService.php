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
            'purchase_orders' => $this->buildPurchaseOrders($filters),
            'supplier_spend' => $this->buildSupplierSpend($filters),
            'supplier_performance' => $this->buildSupplierPerformance($filters),
            'goods_receipts' => $this->buildGoodsReceipts($filters),
            'purchase_invoices' => $this->buildPurchaseInvoices($filters),
            'outstanding_supplier_payments' => $this->buildOutstandingSupplierPayments($filters),
            'lead_time_report' => $this->buildLeadTimeReport($filters),
            'bin_utilization' => $this->buildBinUtilization($filters),
            'warehouse_utilization' => $this->buildWarehouseUtilization($filters),
            'movement_report' => $this->buildMovementReport($filters),
            'picking_report' => $this->buildPickingReport($filters),
            'packing_report' => $this->buildPackingReport($filters),
            'returns_report' => $this->buildReturnsReport($filters),
            'cycle_count_report' => $this->buildCycleCountReport($filters),
            'warehouse_productivity' => $this->buildWarehouseProductivity($filters),
            'asset_register' => $this->buildAssetRegister($filters),
            'asset_depreciation' => $this->buildAssetDepreciation($filters),
            'asset_maintenance' => $this->buildAssetMaintenance($filters),
            'material_cost_by_activity' => $this->buildMaterialCostByActivity($filters),
            'material_quantity_by_activity' => $this->buildMaterialQuantityByActivity($filters),
            'most_expensive_activities' => $this->buildMostExpensiveActivities($filters),
            'most_consumed_materials' => $this->buildMostConsumedMaterials($filters),
            'activity_material_summary' => $this->buildActivityMaterialSummary($filters),
            
            // Sprint 29.6 Project Cost Utilization Dashboard & Analytics
            'project_budget_vs_actual' => $this->buildProjectBudgetVsActual($filters),
            'project_cost_breakdown' => $this->buildProjectCostBreakdown($filters),
            'project_material_utilization' => $this->buildProjectMaterialUtilization($filters),
            'activity_cost_summary' => $this->buildActivityCostSummary($filters),
            'budget_variance' => $this->buildProjectBudgetVsActual($filters), // Same underlying data
            'top_cost_drivers' => $this->buildTopCostDrivers($filters),
            'project_cost_summary' => $this->buildProjectCostSummary($filters),
            'cost_by_category' => $this->buildCostByCategory($filters),
            'material_usage_summary' => $this->buildMaterialUsageSummary($filters),
            'project_financial_overview' => $this->buildProjectFinancialOverview($filters),

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

    protected function buildPurchaseOrders(array $filters)
    {
        $query = \App\Models\PurchaseOrder::with(['supplier', 'creator']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['supplier_id'])) {
            $query->whereIn('supplier_id', (array) $filters['supplier_id']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        
        $this->applyDateFilters($query, $filters, 'order_date');

        return $query->get();
    }

    protected function buildSupplierSpend(array $filters)
    {
        $query = \App\Models\Supplier::query();
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['supplier_id'])) {
            $query->whereIn('id', (array) $filters['supplier_id']);
        }

        $suppliers = $query->get();

        $suppliers->each(function($supplier) use ($filters) {
            $paymentQuery = \App\Models\SupplierPayment::where('supplier_id', $supplier->id);
            $this->applyDateFilters($paymentQuery, $filters, 'payment_date');
            
            $supplier->total_spend = $paymentQuery->sum('amount');
        });

        return $suppliers->sortByDesc('total_spend')->values();
    }

    protected function buildSupplierPerformance(array $filters)
    {
        $query = \App\Models\Supplier::query();
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['supplier_id'])) {
            $query->whereIn('id', (array) $filters['supplier_id']);
        }

        $suppliers = $query->get();

        $suppliers->each(function($supplier) use ($filters) {
            $poQuery = \App\Models\PurchaseOrder::where('supplier_id', $supplier->id);
            $this->applyDateFilters($poQuery, $filters, 'order_date');
            
            $totalOrders = $poQuery->count();
            $completedOrders = (clone $poQuery)->where('status', 'completed')->count();
            
            $supplier->total_orders = $totalOrders;
            $supplier->completed_orders = $completedOrders;
            $supplier->fulfillment_rate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
            // Additional performance metrics can be computed here (e.g. defect rate)
        });

        return $suppliers;
    }

    protected function buildGoodsReceipts(array $filters)
    {
        $query = \App\Models\GoodsReceipt::with(['purchaseOrder.supplier']);
        $this->applyCommonFilters($query, $filters);
        
        $this->applyDateFilters($query, $filters, 'receipt_date');

        return $query->get();
    }

    protected function buildPurchaseInvoices(array $filters)
    {
        $query = \App\Models\PurchaseInvoice::with(['supplier', 'purchaseOrder']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['supplier_id'])) {
            $query->whereIn('supplier_id', (array) $filters['supplier_id']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        
        $this->applyDateFilters($query, $filters, 'invoice_date');

        return $query->get();
    }

    protected function buildOutstandingSupplierPayments(array $filters)
    {
        $query = \App\Models\PurchaseInvoice::with(['supplier'])
            ->whereIn('status', ['draft', 'partially_paid']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['supplier_id'])) {
            $query->whereIn('supplier_id', (array) $filters['supplier_id']);
        }
        
        $invoices = $query->get();
        
        $invoices->each(function ($invoice) {
            $days = $invoice->due_date ? $invoice->due_date->diffInDays(now(), false) : 0;
            $invoice->aging_days = max(0, $days);
            $invoice->outstanding_amount = $invoice->grand_total - $invoice->paid_amount;
            
            if ($invoice->aging_days <= 0) {
                $invoice->aging_bucket = 'Current';
            } elseif ($invoice->aging_days <= 30) {
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
    protected function buildLeadTimeReport(array $filters)
    {
        $query = \App\Models\PurchaseOrder::with(['supplier', 'goodsReceipts'])
            ->where('status', 'completed')
            ->whereHas('goodsReceipts');
            
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'order_date');
        
        $orders = $query->get();
        
        // Group by supplier and calculate average lead time
        $suppliers = [];
        
        foreach ($orders as $order) {
            $supplierId = $order->supplier_id;
            
            if (!isset($suppliers[$supplierId])) {
                $suppliers[$supplierId] = [
                    'supplier' => $order->supplier,
                    'total_lead_time_days' => 0,
                    'order_count' => 0,
                ];
            }
            
            // Get first goods receipt date
            $firstReceipt = $order->goodsReceipts->sortBy('receipt_date')->first();
            if ($firstReceipt && $order->order_date) {
                $days = $order->order_date->diffInDays($firstReceipt->receipt_date);
                $suppliers[$supplierId]['total_lead_time_days'] += max(0, $days);
                $suppliers[$supplierId]['order_count']++;
            }
        }
        
        $results = collect();
        foreach ($suppliers as $data) {
            if ($data['order_count'] > 0) {
                $results->push((object)[
                    'supplier' => $data['supplier'],
                    'average_lead_time' => round($data['total_lead_time_days'] / $data['order_count'], 2),
                    'order_count' => $data['order_count']
                ]);
            }
        }
        
        return $results->sortBy('average_lead_time')->values();
    }

    protected function buildBinUtilization(array $filters)
    {
        $query = \App\Models\WarehouseBin::with(['warehouse', 'zone']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['warehouse_id'])) {
            $query->whereIn('warehouse_id', (array) $filters['warehouse_id']);
        }

        $bins = $query->get();
        $bins->each(function($bin) {
            $bin->utilization_percentage = $bin->capacity > 0 ? round(($bin->current_quantity / $bin->capacity) * 100, 2) : 0;
        });

        return $bins;
    }

    protected function buildWarehouseUtilization(array $filters)
    {
        $query = \App\Models\Warehouse::query();
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['warehouse_id'])) {
            $query->whereIn('id', (array) $filters['warehouse_id']);
        }

        $warehouses = $query->get();
        
        $warehouses->each(function($warehouse) {
            $bins = \App\Models\WarehouseBin::where('warehouse_id', $warehouse->id)->get();
            $totalCapacity = $bins->sum('capacity');
            $currentQuantity = $bins->sum('current_quantity');
            $activeBins = $bins->where('status', 'active')->count();
            
            $warehouse->total_bins = $bins->count();
            $warehouse->active_bins = $activeBins;
            $warehouse->total_capacity = $totalCapacity;
            $warehouse->current_quantity = $currentQuantity;
            $warehouse->utilization_percentage = $totalCapacity > 0 ? round(($currentQuantity / $totalCapacity) * 100, 2) : 0;
        });

        return $warehouses;
    }

    protected function buildMovementReport(array $filters)
    {
        $query = \App\Models\WarehouseMovement::with(['sourceWarehouse', 'destinationWarehouse', 'creator']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'created_at');
        return $query->get();
    }

    protected function buildPickingReport(array $filters)
    {
        $query = \App\Models\WarehousePicking::with(['warehouse', 'tasks']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'created_at');
        return $query->get();
    }

    protected function buildPackingReport(array $filters)
    {
        $query = \App\Models\WarehousePacking::with(['warehouse', 'picking', 'packer']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'created_at');
        return $query->get();
    }

    protected function buildReturnsReport(array $filters)
    {
        $query = \App\Models\WarehouseReturn::with(['warehouse', 'inspector']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'created_at');
        return $query->get();
    }

    protected function buildCycleCountReport(array $filters)
    {
        $query = \App\Models\WarehouseCycleCount::with(['warehouse', 'stockCount.items']);
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'created_at');
        return $query->get();
    }

    protected function buildWarehouseProductivity(array $filters)
    {
        $query = \App\Models\WarehouseTask::with(['assignee', 'warehouse'])
            ->where('status', 'completed');
        $this->applyCommonFilters($query, $filters);
        $this->applyDateFilters($query, $filters, 'completed_at');
        
        $tasks = $query->get();
        $users = [];
        
        foreach ($tasks as $task) {
            $userId = $task->taskable->created_by ?? $task->company_id; // Approximation based on task associations if needed, or if task has assigned_to
            // For now, let's group by warehouse
            $warehouseId = $task->warehouse_id;
            if (!isset($users[$warehouseId])) {
                $users[$warehouseId] = [
                    'warehouse' => $task->warehouse,
                    'total_tasks' => 0,
                    'picking_tasks' => 0,
                    'put_away_tasks' => 0,
                ];
            }
            $users[$warehouseId]['total_tasks']++;
            if ($task->type === 'picking') $users[$warehouseId]['picking_tasks']++;
            if ($task->type === 'put_away') $users[$warehouseId]['put_away_tasks']++;
        }
        
        return collect(array_values($users));
    }

    protected function buildAssetRegister(array $filters)
    {
        $query = \App\Models\Asset::with(['category', 'assignedUser', 'department', 'branch', 'warehouse']);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        if (!empty($filters['asset_category_id'])) {
            $query->whereIn('asset_category_id', (array) $filters['asset_category_id']);
        }
        
        $this->applyDateFilters($query, $filters, 'purchase_date');

        return $query->get();
    }

    protected function buildAssetDepreciation(array $filters)
    {
        $query = \App\Models\AssetDepreciation::with(['asset.category', 'journal']);
        
        if (!empty($filters['company_id'])) {
            $query->whereHas('asset', function($q) use ($filters) {
                $q->whereIn('company_id', (array) $filters['company_id']);
            });
        }
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        
        $this->applyDateFilters($query, $filters, 'period_date');

        return $query->get();
    }

    protected function buildAssetMaintenance(array $filters)
    {
        $query = \App\Models\AssetMaintenance::with(['asset.category']);
        
        if (!empty($filters['company_id'])) {
            $query->whereHas('asset', function($q) use ($filters) {
                $q->whereIn('company_id', (array) $filters['company_id']);
            });
        }
        
        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
        
        $this->applyDateFilters($query, $filters, 'maintenance_date');

        return $query->get();
    }

    protected function buildMaterialCostByActivity(array $filters)
    {
        $query = Task::query()->with(['project'])->where('actual_material_cost', '>', 0);
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['project_id'])) {
            $query->whereIn('project_id', (array) $filters['project_id']);
        }
        
        return $query->orderByDesc('actual_material_cost')->get();
    }

    protected function buildMaterialQuantityByActivity(array $filters)
    {
        $query = \App\Models\ProjectMaterialIssueItem::query()
            ->join('project_material_issues', 'project_material_issue_items.project_material_issue_id', '=', 'project_material_issues.id')
            ->join('tasks', 'project_material_issues.task_id', '=', 'tasks.id')
            ->join('products', 'project_material_issue_items.product_id', '=', 'products.id')
            ->select('tasks.id as task_id', 'tasks.name as task_name', 'products.name as product_name', DB::raw('SUM(project_material_issue_items.quantity) as total_quantity'))
            ->whereNotNull('project_material_issues.task_id')
            ->groupBy('tasks.id', 'tasks.name', 'products.name');

        if (!empty($filters['project_id'])) {
            $query->whereIn('project_material_issues.project_id', (array) $filters['project_id']);
        }

        return collect($query->get());
    }

    protected function buildMostExpensiveActivities(array $filters)
    {
        return $this->buildMaterialCostByActivity($filters)->take(10);
    }

    protected function buildMostConsumedMaterials(array $filters)
    {
        $query = \App\Models\ProjectMaterialIssueItem::query()
            ->join('project_material_issues', 'project_material_issue_items.project_material_issue_id', '=', 'project_material_issues.id')
            ->join('products', 'project_material_issue_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('SUM(project_material_issue_items.quantity) as total_quantity'), DB::raw('SUM(project_material_issue_items.total_cost) as total_cost'))
            ->whereNotNull('project_material_issues.task_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity');

        if (!empty($filters['project_id'])) {
            $query->whereIn('project_material_issues.project_id', (array) $filters['project_id']);
        }

        return collect($query->get())->take(10);
    }

    protected function buildActivityMaterialSummary(array $filters)
    {
        $query = Task::query()->with(['project', 'materialIssues.items.product'])->whereHas('materialIssues');
        $this->applyCommonFilters($query, $filters);
        
        if (!empty($filters['project_id'])) {
            $query->whereIn('project_id', (array) $filters['project_id']);
        }
        
        return $query->get();
    }

    protected function buildProjectBudgetVsActual(array $filters)
    {
        $query = Project::query()->with(['client', 'manager']);
        $this->applyCommonFilters($query, $filters);
        if (!empty($filters['project_id'])) {
            $query->whereIn('id', (array) $filters['project_id']);
        }
        $projects = $query->get();
        $projects->each(function($project) {
            $project->variance = $project->actual_budget - $project->actual_cost;
            $project->budget_utilization_percent = $project->actual_budget > 0 ? ($project->actual_cost / $project->actual_budget) * 100 : 0;
            if ($project->budget_utilization_percent > 100) {
                $project->status_indicator = 'Over Budget';
            } elseif ($project->budget_utilization_percent >= 80) {
                $project->status_indicator = 'Warning';
            } else {
                $project->status_indicator = 'Healthy';
            }
        });
        return $projects;
    }

    protected function buildProjectCostBreakdown(array $filters)
    {
        $query = Project::query()->with(['client']);
        $this->applyCommonFilters($query, $filters);
        if (!empty($filters['project_id'])) {
            $query->whereIn('id', (array) $filters['project_id']);
        }
        $projects = $query->get();
        $projects->each(function($project) {
            // Reusing existing data sources without rewriting accounting engines.
            $project->material_cost = Task::where('project_id', $project->id)->sum('actual_material_cost');
            $project->equipment_cost = 0; // Placeholder for equipment tracking
            $project->procurement_cost = \App\Models\PurchaseOrder::where('project_id', $project->id)->sum('grand_total') ?? 0;
            $project->general_expenses = GeneralLedger::where('project_id', $project->id)
                ->whereHas('chartOfAccount.accountType', function($q) {
                    $q->where('category', 'Expense');
                })->sum('debit');
        });
        return $projects;
    }

    protected function buildProjectMaterialUtilization(array $filters)
    {
        $query = Project::query()->withSum('tasks', 'actual_material_cost');
        $this->applyCommonFilters($query, $filters);
        if (!empty($filters['project_id'])) {
            $query->whereIn('id', (array) $filters['project_id']);
        }
        $projects = $query->get();
        $projects->each(function($project) {
            $project->total_material_cost = $project->tasks_sum_actual_material_cost ?? 0;
        });
        return $projects->sortByDesc('total_material_cost')->values();
    }

    protected function buildActivityCostSummary(array $filters)
    {
        $query = Task::query()->with(['project', 'assignee']);
        $this->applyCommonFilters($query, $filters);
        if (!empty($filters['project_id'])) {
            $query->whereIn('project_id', (array) $filters['project_id']);
        }
        $tasks = $query->get();
        $tasks->each(function($task) {
            $task->total_cost = $task->actual_material_cost; // Expand if labor cost exists
            $task->cost_per_day = $task->start_date && $task->due_date ? 
                $task->total_cost / max(1, $task->start_date->diffInDays($task->due_date)) : 0;
        });
        return $tasks->sortByDesc('total_cost')->values();
    }

    protected function buildTopCostDrivers(array $filters)
    {
        // Combined view of highest cost projects and activities
        $projects = $this->buildProjectBudgetVsActual($filters)->sortByDesc('actual_cost')->take(5);
        $activities = $this->buildActivityCostSummary($filters)->take(5);
        
        return collect([
            'projects' => $projects,
            'activities' => $activities
        ]);
    }

    protected function buildProjectCostSummary(array $filters)
    {
        return $this->buildProjectBudgetVsActual($filters); // Uses same base dataset
    }

    protected function buildCostByCategory(array $filters)
    {
        $companyId = $filters['company_id'] ?? auth()->user()?->company_id ?? 1;
        $query = GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('category', 'Expense');
            })
            ->join('chart_of_accounts', 'general_ledgers.chart_of_account_id', '=', 'chart_of_accounts.id')
            ->select('chart_of_accounts.name as category_name', DB::raw('SUM(general_ledgers.debit) as total_cost'))
            ->groupBy('chart_of_accounts.name');
            
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        return $query->get()->sortByDesc('total_cost')->values();
    }

    protected function buildMaterialUsageSummary(array $filters)
    {
        return $this->buildMostConsumedMaterials($filters);
    }

    protected function buildProjectFinancialOverview(array $filters)
    {
        $projects = $this->buildProjectBudgetVsActual($filters);
        
        return collect([
            'total_estimated_budget' => $projects->sum('estimated_budget'),
            'total_actual_budget' => $projects->sum('actual_budget'),
            'total_actual_cost' => $projects->sum('actual_cost'),
            'total_remaining_budget' => $projects->sum('variance'),
            'projects_count' => $projects->count(),
            'over_budget_count' => $projects->where('status_indicator', 'Over Budget')->count(),
            'healthy_count' => $projects->where('status_indicator', 'Healthy')->count(),
        ]);
    }
}
