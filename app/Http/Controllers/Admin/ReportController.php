<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\FavoriteReport;
use App\Services\ReportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected ExportService $exportService;

    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    /**
     * Resolve date range from filter parameters.
     */
    private function resolveDateRange(Request $request): array
    {
        $preset = $request->input('date_preset', 'all');
        $dateFromInput = $request->input('date_from');
        $dateToInput   = $request->input('date_to');

        // If custom preset or direct date inputs are provided
        if ($preset === 'custom' || ($dateFromInput && $dateToInput)) {
            $dateFrom = $dateFromInput ? Carbon::parse($dateFromInput)->startOfDay() : null;
            $dateTo   = $dateToInput   ? Carbon::parse($dateToInput)->endOfDay()     : null;
            return [$dateFrom, $dateTo, 'custom'];
        }

        $dateFrom = null;
        $dateTo   = null;

        switch ($preset) {
            case 'today':
                $dateFrom = Carbon::today();
                $dateTo   = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $dateFrom = Carbon::yesterday();
                $dateTo   = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $dateFrom = Carbon::now()->startOfWeek();
                $dateTo   = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $dateFrom = Carbon::now()->subWeek()->startOfWeek();
                $dateTo   = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo   = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $dateFrom = Carbon::now()->subMonth()->startOfMonth();
                $dateTo   = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $dateFrom = Carbon::now()->startOfQuarter();
                $dateTo   = Carbon::now()->endOfQuarter();
                break;
            case 'this_year':
                $dateFrom = Carbon::now()->startOfYear();
                $dateTo   = Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $dateFrom = Carbon::now()->subYear()->startOfYear();
                $dateTo   = Carbon::now()->subYear()->endOfYear();
                break;
            default:
                $preset = 'all';
                break;
        }

        return [$dateFrom, $dateTo, $preset];
    }

    /**
     * Build filtered dashboard statistics.
     */
    private function buildDashboardStats(?int $companyId, ?Carbon $dateFrom, ?Carbon $dateTo, ?int $clientId, ?int $projectId = null): array
    {
        // --- Invoice base query ---
        $invoiceQuery = \App\Models\Invoice::query();
        if ($companyId) $invoiceQuery->where('company_id', $companyId);
        if ($dateFrom)  $invoiceQuery->where('issue_date', '>=', $dateFrom);
        if ($dateTo)    $invoiceQuery->where('issue_date', '<=', $dateTo);
        if ($clientId)  $invoiceQuery->where('client_id', $clientId);
        if ($projectId) $invoiceQuery->where('project_id', $projectId);

        // --- Payment base query ---
        $paymentQuery = \App\Models\Payment::query();
        if ($companyId) $paymentQuery->where('company_id', $companyId);
        if ($dateFrom)  $paymentQuery->where('payment_date', '>=', $dateFrom);
        if ($dateTo)    $paymentQuery->where('payment_date', '<=', $dateTo);
        if ($clientId)  $paymentQuery->where('client_id', $clientId);
        if ($projectId) $paymentQuery->where('project_id', $projectId);

        // KPIs
        $totalInvoiced    = (clone $invoiceQuery)->sum('total_amount');
        $totalPaid        = (clone $paymentQuery)->sum('amount');
        $totalOutstanding = (clone $invoiceQuery)->sum('balance_due');
        $invoiceCount     = (clone $invoiceQuery)->count();
        $overdueCount     = (clone $invoiceQuery)->where('status', 'overdue')->count();
        $avgInvoice       = $invoiceCount > 0 ? round($totalInvoiced / $invoiceCount, 2) : 0;
        $collectionRate   = $totalInvoiced > 0 ? round(($totalPaid / $totalInvoiced) * 100, 1) : 0;

        // Previous period comparison (same length window)
        $prevTotalPaid = 0;
        if ($dateFrom && $dateTo) {
            $diff = $dateTo->diffInSeconds($dateFrom);
            $prevFrom = (clone $dateFrom)->subSeconds($diff);
            $prevTo   = (clone $dateFrom)->subSecond();
            $prevQuery = \App\Models\Payment::query()
                ->whereBetween('payment_date', [$prevFrom, $prevTo]);
            if ($companyId) $prevQuery->where('company_id', $companyId);
            if ($clientId)  $prevQuery->where('client_id', $clientId);
            if ($projectId) $prevQuery->where('project_id', $projectId);
            $prevTotalPaid = $prevQuery->sum('amount');
        }
        $revenueGrowth = $prevTotalPaid > 0 ? round((($totalPaid - $prevTotalPaid) / $prevTotalPaid) * 100, 1) : null;

        // Invoice Status Breakdown
        $invoiceStatusData = (clone $invoiceQuery)
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('status')
            ->get();

        // Payments by Method
        $paymentMethodData = (clone $paymentQuery)
            ->with('paymentMethod')
            ->select('payment_method_id', DB::raw('sum(amount) as total'))
            ->whereNotNull('payment_method_id')
            ->groupBy('payment_method_id')
            ->get();

        // Monthly trend (last 6 periods)
        $trendFrom = $dateFrom ?? now()->subMonths(5)->startOfMonth();
        $trendPayments = \App\Models\Payment::query()
            ->where('payment_date', '>=', $trendFrom);
        if ($companyId) $trendPayments->where('company_id', $companyId);
        if ($dateTo)    $trendPayments->where('payment_date', '<=', $dateTo);
        if ($clientId)  $trendPayments->where('client_id', $clientId);
        if ($projectId) $trendPayments->where('project_id', $projectId);
        $allPayments = $trendPayments->get();

        $monthlyPayments = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyPayments->push([
                'label' => $month->format('M Y'),
                'total' => (float) $allPayments->filter(fn($p) =>
                    $p->payment_date && $p->payment_date->format('Y-m') === $month->format('Y-m')
                )->sum('amount'),
            ]);
        }

        // Top Clients by revenue (invoiced)
        $topClientsQuery = \App\Models\Invoice::query()
            ->with('client')
            ->select('client_id', DB::raw('sum(total_amount) as total'), DB::raw('count(*) as invoice_count'))
            ->whereNotNull('client_id')
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->limit(5);
        if ($companyId) $topClientsQuery->where('company_id', $companyId);
        if ($dateFrom)  $topClientsQuery->where('issue_date', '>=', $dateFrom);
        if ($dateTo)    $topClientsQuery->where('issue_date', '<=', $dateTo);
        if ($clientId)  $topClientsQuery->where('client_id', $clientId);
        if ($projectId) $topClientsQuery->where('project_id', $projectId);
        $topClients = $topClientsQuery->get();

        return compact(
            'totalInvoiced', 'totalPaid', 'totalOutstanding',
            'invoiceCount', 'overdueCount', 'avgInvoice', 'collectionRate', 'revenueGrowth',
            'invoiceStatusData', 'paymentMethodData', 'monthlyPayments', 'topClients'
        );
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', ReportTemplate::class);

        $userId = auth()->id();
        $user   = auth()->user();

        $selectedCompanyId = $request->filled('company_id') ? (int) $request->input('company_id') : ($user->company_id ?? null);
        $clientId          = $request->filled('client_id') ? (int) $request->input('client_id') : null;
        $projectId         = $request->filled('project_id') ? (int) $request->input('project_id') : null;

        [$dateFrom, $dateTo, $datePreset] = $this->resolveDateRange($request);

        $stats = $this->buildDashboardStats($selectedCompanyId, $dateFrom, $dateTo, $clientId, $projectId);

        // Filter options for inputs
        $companies = \App\Models\Company::select('id', 'name')->orderBy('name')->get();

        $projectsQuery = \App\Models\Project::query()->select('id', 'name', 'company_id')->orderBy('name');
        if ($selectedCompanyId) {
            $projectsQuery->where('company_id', $selectedCompanyId);
        }
        $projects = $projectsQuery->get();

        $clientsQuery = \App\Models\Client::query()->select('id', 'display_name', 'first_name', 'last_name', 'company_name', 'company_id')->orderBy('display_name');
        if ($selectedCompanyId) {
            $clientsQuery->where('company_id', $selectedCompanyId);
        }
        $clients = $clientsQuery->get();

        $systemTemplates   = $this->reportService->getSystemTemplates();
        $userTemplates     = $this->reportService->getUserTemplates($userId);
        $favoriteTemplates = $this->reportService->getFavoriteTemplates($userId);

        return view('admin.reports.index', array_merge($stats, compact(
            'systemTemplates', 'userTemplates', 'favoriteTemplates',
            'companies', 'projects', 'clients',
            'datePreset', 'dateFrom', 'dateTo',
            'selectedCompanyId', 'projectId', 'clientId'
        )));
    }

    /**
     * Generate & stream a PDF of the filtered dashboard.
     */
    public function dashboardPdf(Request $request)
    {
        Gate::authorize('viewAny', ReportTemplate::class);

        $companyId = auth()->user()->company_id ?? 1;
        [$dateFrom, $dateTo, $datePreset] = $this->resolveDateRange($request);
        $clientId  = $request->input('client_id') ? (int) $request->input('client_id') : null;

        $stats  = $this->buildDashboardStats($companyId, $dateFrom, $dateTo, $clientId);
        $client = $clientId ? \App\Models\Client::find($clientId) : null;

        $pdf = Pdf::loadView('admin.reports.exports.dashboard-pdf', array_merge($stats, [
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'datePreset' => $datePreset,
            'client'     => $client,
            'companyId'  => $companyId,
        ]))->setPaper('a4', 'portrait');

        $filename = 'reports-dashboard-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function builder(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        // Can pass existing template if editing
        $template = null;
        if ($request->has('template_id')) {
            $template = ReportTemplate::findOrFail($request->template_id);
            Gate::authorize('view', $template);
        }

        $options = [
            'companies' => \App\Models\Company::select('id', 'name')->get(),
            'branches' => \App\Models\Branch::select('id', 'name', 'company_id')->get(),
            'departments' => \App\Models\Department::select('id', 'name', 'branch_id')->get(),
            'projects' => \App\Models\Project::select('id', 'name')->get(),
            'clients' => \App\Models\Client::select('id', 'display_name', 'company_name', 'first_name', 'last_name')->get()->map(function ($client) {
                return (object) [
                    'id' => $client->id,
                    'name' => $client->name,
                ];
            }),
            'users' => \App\Models\User::select('id', 'name', 'department_id')->get(),
            'warehouses' => \App\Models\Warehouse::select('id', 'name', 'company_id')->get(),
            'suppliers' => \App\Models\Supplier::select('id', 'name', 'company_id')->get(),
        ];

        return view('admin.reports.builder', compact('template', 'options'));
    }

    public function preview(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        $type = $request->input('type');
        $filters = $request->input('filters', []);
        
        // We create a dummy template in memory to pass to getReportData
        $template = new ReportTemplate([
            'name' => 'Preview',
            'type' => $type,
            'filters' => $filters,
            'layout' => $request->input('layout', []),
        ]);

        $reportData = $this->reportService->getReportData($template, $filters);
        
        // Return a partial view or JSON. Let's return JSON for Alpine to render or HTML.
        // For simplicity, returning rendered HTML of just the viewer content.
        return view('admin.reports.partials.viewer-content', $reportData)->render();
    }

    public function show(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('view', $reportTemplate);

        $filters = $request->except(['_token', 'page']);
        $reportData = $this->reportService->getReportData($reportTemplate, $filters);

        $this->reportService->logActivity('report_generated', $reportTemplate);

        return view('admin.reports.viewer', $reportData);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ReportTemplate::class);

        if (is_string($request->input('layout'))) {
            $request->merge(['layout' => json_decode($request->input('layout'), true)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'filters' => 'nullable|array',
            'layout' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_system'] = false;

        $template = ReportTemplate::create($validated);
        
        $this->reportService->logActivity('template_created', $template);

        return redirect()->route('admin.reports.show', $template)->with('success', 'Report Template saved successfully.');
    }

    public function update(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('update', $reportTemplate);

        if (is_string($request->input('layout'))) {
            $request->merge(['layout' => json_decode($request->input('layout'), true)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'filters' => 'nullable|array',
            'layout' => 'nullable|array',
        ]);

        $reportTemplate->update($validated);

        return redirect()->route('admin.reports.show', $reportTemplate)->with('success', 'Report Template updated successfully.');
    }

    public function destroy(ReportTemplate $reportTemplate)
    {
        Gate::authorize('delete', $reportTemplate);

        $reportTemplate->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Report Template deleted successfully.');
    }

    public function favorite(ReportTemplate $reportTemplate)
    {
        Gate::authorize('view', $reportTemplate);

        $favorite = FavoriteReport::where('user_id', auth()->id())
            ->where('report_template_id', $reportTemplate->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Report removed from favorites.');
        }

        FavoriteReport::create([
            'user_id' => auth()->id(),
            'report_template_id' => $reportTemplate->id,
        ]);

        return back()->with('success', 'Report added to favorites.');
    }

    public function export(Request $request, ReportTemplate $reportTemplate)
    {
        Gate::authorize('export', $reportTemplate);

        $validated = $request->validate([
            'format' => 'required|in:pdf,xlsx,csv',
        ]);

        $filters = $request->except(['_token', 'format']);
        
        $exportHistory = $this->exportService->export(
            $reportTemplate,
            $filters,
            $validated['format'],
            auth()->id(),
            auth()->user()->company_id
        );

        $this->reportService->logActivity('report_exported', $reportTemplate);

        $exportHistory->refresh();
        if ($exportHistory->status === 'completed' && $exportHistory->file_path) {
            return \Illuminate\Support\Facades\Storage::disk('local')->download($exportHistory->file_path);
        }

        return back()->with('error', 'Export failed: ' . $exportHistory->error_message);
    }
}
