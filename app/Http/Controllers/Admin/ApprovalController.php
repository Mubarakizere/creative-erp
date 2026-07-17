<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Http\Requests\ApprovalActionRequest;
use App\Services\ApprovalService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApprovalController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected ApprovalService $approvalService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', Approval::class);
        
        $userId = auth()->id();
        $roleIds = auth()->user() ? auth()->user()->roles->pluck('id')->toArray() : [];

        $myApprovals = Approval::with('approvable', 'workflow', 'currentStep')
            ->where('status', 'Pending Approval')
            ->whereHas('currentStep', function ($query) use ($userId, $roleIds) {
                $query->where('approver_user_id', $userId)
                      ->orWhereIn('approver_role_id', $roleIds);
            })
            ->latest('submitted_at')
            ->paginate(10, ['*'], 'approvals_page');

        $myRequests = Approval::with('approvable', 'workflow', 'currentStep')
            ->where('submitted_by', $userId)
            ->latest('submitted_at')
            ->paginate(10, ['*'], 'requests_page');

        return view('admin.approvals.index', compact('myApprovals', 'myRequests'));
    }

    public function show(Approval $approval)
    {
        $this->authorize('view', $approval);
        $approval->load('approvable', 'workflow.steps', 'currentStep', 'actions.user', 'actions.step');
        return view('admin.approvals.show', compact('approval'));
    }

    public function action(ApprovalActionRequest $request, Approval $approval)
    {
        $action = $request->validated('action');
        $comment = $request->validated('comment');

        try {
            switch ($action) {
                case 'approve':
                    $this->authorize('approve', $approval);
                    $this->approvalService->approve($approval, $comment);
                    $message = 'Request approved successfully.';
                    break;
                case 'reject':
                    $this->authorize('reject', $approval);
                    $this->approvalService->reject($approval, $comment);
                    $message = 'Request rejected.';
                    break;
                case 'return':
                    $this->authorize('return', $approval);
                    $this->approvalService->returnForRevision($approval, $comment);
                    $message = 'Request returned for revision.';
                    break;
                case 'cancel':
                    $this->authorize('cancel', $approval);
                    $this->approvalService->cancel($approval, $comment);
                    $message = 'Request cancelled.';
                    break;
                case 'resubmit':
                    $this->authorize('submit', $approval);
                    $this->approvalService->resubmit($approval, $comment);
                    $message = 'Request resubmitted successfully.';
                    break;
                default:
                    return back()->with('error', 'Invalid action.');
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
