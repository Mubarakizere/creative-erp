<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkflowMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'pending_approvals' => Approval::where('status', 'Pending Approval')->count(),
            'approved_today' => Approval::where('status', 'Approved')->whereDate('completed_at', Carbon::today())->count(),
            'rejected_today' => Approval::where('status', 'Rejected')->whereDate('completed_at', Carbon::today())->count(),
            'returned_for_revision' => Approval::where('status', 'Returned for Revision')->count(),
            'my_pending_requests' => Approval::where('submitted_by', auth()->id())->where('status', 'Pending Approval')->count(),
            'average_approval_time' => $this->getAverageApprovalTime(),
        ];
    }

    public function widgets(): array
    {
        return [
            'myPendingApprovals' => Approval::with('workflow')->where('submitted_by', auth()->id())->where('status', 'Pending Approval')->latest('submitted_at')->take(5)->get(),
            'recentlyApproved' => Approval::with('workflow')->where('status', 'Approved')->latest('completed_at')->take(5)->get(),
            'recentlyRejected' => Approval::with('workflow')->where('status', 'Rejected')->latest('completed_at')->take(5)->get(),
            'requestsAwaitingMyDecision' => $this->getRequestsAwaitingMyDecision(),
            // Activity Feed is handled via ApprovalAction in real app, simplified here
        ];
    }

    public function reports(): array
    {
        return [
            'approvals_by_module' => Approval::join('approval_workflows', 'approvals.approval_workflow_id', '=', 'approval_workflows.id')
                ->select('approval_workflows.module', DB::raw('count(*) as total'))
                ->groupBy('approval_workflows.module')
                ->get(),
            'approvals_by_status' => Approval::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
        ];
    }

    protected function getAverageApprovalTime(): string
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $avgMinutes = Approval::whereNotNull('completed_at')
                ->whereNotNull('submitted_at')
                ->select(DB::raw('AVG((julianday(completed_at) - julianday(submitted_at)) * 24 * 60) as avg_minutes'))
                ->value('avg_minutes');
        } else {
            $avgMinutes = Approval::whereNotNull('completed_at')
                ->whereNotNull('submitted_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, submitted_at, completed_at)) as avg_minutes'))
                ->value('avg_minutes');
        }

        if (!$avgMinutes) {
            return 'N/A';
        }

        $hours = floor($avgMinutes / 60);
        $minutes = round($avgMinutes % 60);

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    protected function getRequestsAwaitingMyDecision()
    {
        $userId = auth()->id();
        $roleIds = auth()->user() ? auth()->user()->roles->pluck('id')->toArray() : [];

        return Approval::with('workflow', 'currentStep')
            ->where('status', 'Pending Approval')
            ->whereHas('currentStep', function ($query) use ($userId, $roleIds) {
                $query->where('approver_user_id', $userId)
                      ->orWhereIn('approver_role_id', $roleIds);
            })
            ->latest('submitted_at')
            ->take(5)
            ->get();
    }
}
