<?php

namespace App\Listeners;

use App\Events\ApprovalAssigned;
use App\Events\WorkflowApproved;
use App\Events\WorkflowRejected;
use App\Events\MeetingCreated;
use App\Events\MeetingInvitationSent;
use App\Events\MentionDetected;
use App\Notifications\AppNotification;
use App\Services\NotificationService;
use Illuminate\Events\Dispatcher;

class NotificationEventSubscriber
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function handleApprovalAssigned(ApprovalAssigned $event): void
    {
        $approver = $event->step->user;
        if ($approver) {
            $this->notificationService->send($approver, new AppNotification(
                title: 'Approval Required',
                message: "You have a new approval request.",
                category: 'workflow',
                priority: 'High',
                actionUrl: route('admin.approvals.index', ['id' => $event->approval->id]),
                actionText: 'View Approval',
                icon: 'check-circle'
            ));
        }
    }

    public function handleWorkflowApproved(WorkflowApproved $event): void
    {
        $submitter = $event->approval->submitter;
        if ($submitter) {
            $this->notificationService->send($submitter, new AppNotification(
                title: 'Workflow Approved',
                message: "Your workflow request has been approved.",
                category: 'workflow',
                priority: 'Normal',
                icon: 'check'
            ));
        }
    }

    public function handleWorkflowRejected(WorkflowRejected $event): void
    {
        $submitter = $event->approval->submitter;
        if ($submitter) {
            $this->notificationService->send($submitter, new AppNotification(
                title: 'Workflow Rejected',
                message: "Your workflow request has been rejected.",
                category: 'workflow',
                priority: 'High',
                icon: 'x-circle',
                color: 'red'
            ));
        }
    }

    public function handleMeetingInvitationSent(MeetingInvitationSent $event): void
    {
        $this->notificationService->send($event->attendee, new AppNotification(
            title: 'Meeting Invitation',
            message: "You have been invited to a meeting.",
            category: 'meetings',
            priority: 'Normal',
            icon: 'calendar'
        ));
    }

    public function handleMentionDetected(MentionDetected $event): void
    {
        $this->notificationService->send($event->user, new AppNotification(
            title: 'You were mentioned',
            message: "You were mentioned in a comment.",
            category: 'mentions',
            priority: 'Normal',
            icon: 'at-symbol'
        ));
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            ApprovalAssigned::class => 'handleApprovalAssigned',
            WorkflowApproved::class => 'handleWorkflowApproved',
            WorkflowRejected::class => 'handleWorkflowRejected',
            MeetingInvitationSent::class => 'handleMeetingInvitationSent',
            MentionDetected::class => 'handleMentionDetected',
        ];
    }
}
