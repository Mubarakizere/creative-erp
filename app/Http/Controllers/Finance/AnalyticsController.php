<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Metrics\ChartService;

class AnalyticsController extends Controller
{
    protected ChartService $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function index(Request $request)
    {
        $this->authorize('view', \App\Policies\AnalyticsPolicy::class);
        
        $filters = $request->only(['company_id', 'branch_id', 'department_id', 'project_id', 'date_from', 'date_to']);
        if (empty($filters['company_id'])) {
            $filters['company_id'] = auth()->user()->company_id;
        }

        return response()->json([
            'charts' => $this->chartService->getChartData($filters)
        ]);
    }
}
