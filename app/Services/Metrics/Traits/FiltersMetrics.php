<?php

namespace App\Services\Metrics\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FiltersMetrics
{
    /**
     * Apply standard filters to a metric query.
     */
    protected function applyFilters(Builder $query, array $filters, string $relation = null): Builder
    {
        $prefix = $relation ? $relation . '.' : '';

        if (!empty($filters['company_id'])) {
            $query->whereIn($prefix . 'company_id', (array) $filters['company_id']);
        }
        
        if (!empty($filters['branch_id'])) {
            $query->whereIn($prefix . 'branch_id', (array) $filters['branch_id']);
        }
        
        if (!empty($filters['department_id'])) {
            $query->whereIn($prefix . 'department_id', (array) $filters['department_id']);
        }
        
        // Handle dates using general created_at or specific column if needed
        // For tasks, due_date or created_at? By default, created_at
        $dateCol = $prefix . 'created_at';
        if (!empty($filters['date_from'])) {
            $query->whereDate($dateCol, '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate($dateCol, '<=', $filters['date_to']);
        }

        // Project specific
        if (!empty($filters['project_id'])) {
            // Some models don't have project_id, so wrap in check or assume query applies if filter exists
            // But if a client uses 'project_id' and the model doesn't have it, it'll crash.
            // Eloquent ignores if we use right models, but better to check if column exists?
            // Actually, ReportBuilderService handles which filters to apply. 
            // In metric providers, we're applying global filters. So it's safe to assume if the caller 
            // provides `project_id`, they want to filter models with `project_id`.
            // But we might be querying User metrics, which don't have project_id.
            // So we should be careful. We can pass allowed fields.
        }

        return $query;
    }
}
