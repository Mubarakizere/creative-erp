<?php

namespace App\Services\Metrics;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Contact;
use App\Models\Account;
use App\Models\Activity;

class CrmMetrics implements \App\Contracts\MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()->company_id;
        
        $leadsQuery = Lead::query();
        $opportunitiesQuery = Opportunity::query();
        
        if ($companyId) {
            $leadsQuery->where('company_id', $companyId);
            $opportunitiesQuery->where('company_id', $companyId);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $leadsQuery->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
            $opportunitiesQuery->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        $totalLeads = (clone $leadsQuery)->count();
        $totalOpportunities = (clone $opportunitiesQuery)->count();
        
        $wonDeals = (clone $opportunitiesQuery)->where('status', 'Won')->count();
        $lostDeals = (clone $opportunitiesQuery)->where('status', 'Lost')->count();
        $pipelineValue = (clone $opportunitiesQuery)->where('status', 'Open')->sum('expected_revenue');
        
        $totalDeals = (clone $opportunitiesQuery)->whereIn('status', ['Won', 'Lost'])->count();
        $conversionRate = $totalDeals > 0 ? round(($wonDeals / $totalDeals) * 100, 2) : 0;

        return [
            'total_leads' => $totalLeads,
            'total_opportunities' => $totalOpportunities,
            'won_deals' => $wonDeals,
            'lost_deals' => $lostDeals,
            'pipeline_value' => $pipelineValue,
            'conversion_rate' => $conversionRate,
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()->company_id;
        
        $recentDealsQuery = Opportunity::with(['account', 'owner'])->latest()->take(5);
        $upcomingActivitiesQuery = Activity::where('status', 'Pending')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->with(['activityable'])
            ->orderBy('scheduled_at')
            ->take(5);
            
        if ($companyId) {
            $recentDealsQuery->where('company_id', $companyId);
            $upcomingActivitiesQuery->where('company_id', $companyId);
        }

        $recentDeals = $recentDealsQuery->get();
        $upcomingActivities = $upcomingActivitiesQuery->get();

        return [
            'recentDeals' => $recentDeals,
            'upcomingActivities' => $upcomingActivities,
        ];
    }

    public function reports(array $filters = []): array
    {
        // Integration with ReportBuilderService if needed
        return [];
    }
}
