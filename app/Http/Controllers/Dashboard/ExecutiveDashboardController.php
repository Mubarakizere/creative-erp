<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Metrics\MetricsService;
use App\Services\Metrics\ExecutiveMetrics;

class ExecutiveDashboardController extends Controller
{
    use \App\Traits\LogsActivity;

    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function index(Request $request)
    {
        $this->authorize('view', \App\Policies\ExecutiveDashboardPolicy::class);
        
        $filters = $request->only([
            'company_id', 'branch_id', 'department_id', 'project_id', 'client_id', 
            'currency_code', 'fiscal_year_id', 'date_from', 'date_to'
        ]);
        if (empty($filters['company_id'])) {
            $filters['company_id'] = auth()->user()->company_id;
        }

        // Just fetching the ExecutiveMetrics specifically for the dashboard endpoint
        $executiveMetrics = app(ExecutiveMetrics::class);
        
        $this->logActivity('executive_dashboard_accessed', auth()->user());
        
        return response()->json([
            'cards' => $executiveMetrics->cards($filters),
            // The dashboard would also pull from ChartService, etc.
            'charts' => app(\App\Services\Metrics\ChartService::class)->getChartData($filters)
        ]);
    }
}
