<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    use \App\Traits\LogsActivity;

    /**
     * @var DashboardService
     */
    protected $dashboardService;

    /**
     * DashboardController constructor.
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the admin dashboard with statistics.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $filters = $request->only([
            'branch_id',
            'department_id',
            'project_id',
            'client_id',
            'currency_code',
            'fiscal_year_id',
            'accounting_period_id'
        ]);

        $data = $this->dashboardService->getDashboardData($filters);
        
        $companyId = auth()->user()->company_id ?? 1;
        
        $data['filters'] = $filters;
        $data['fiscalYears'] = \App\Models\FiscalYear::where('company_id', $companyId)->orderBy('start_date', 'desc')->get();
        $data['fiscalYearId'] = $request->input('fiscal_year_id');
        $data['branches'] = \App\Models\Branch::where('company_id', $companyId)->get();
        $data['departments'] = \App\Models\Department::where('company_id', $companyId)->get();
        $data['projects'] = \App\Models\Project::where('company_id', $companyId)->get();
        $data['clients'] = \App\Models\Client::where('company_id', $companyId)->get();

        $this->logActivity('dashboard_accessed', auth()->user());

        return view('admin.dashboard.index', $data);
    }
}
