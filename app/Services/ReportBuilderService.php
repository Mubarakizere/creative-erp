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
use App\Services\Metrics\ReportMetrics;
use Illuminate\Database\Eloquent\Builder;

class ReportBuilderService
{
    protected ReportMetrics $reportMetrics;

    public function __construct(ReportMetrics $reportMetrics)
    {
        $this->reportMetrics = $reportMetrics;
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
}
