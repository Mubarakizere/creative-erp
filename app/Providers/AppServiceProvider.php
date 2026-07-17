<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\MeetingPolicy;
use App\Models\Meeting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Notification::observe(\App\Observers\NotificationObserver::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Meeting::class, MeetingPolicy::class);

        // Comment Events
        Event::listen(\App\Events\CommentCreated::class, \App\Listeners\LogActivityListener::class);
        Event::listen(\App\Events\CommentCreated::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        Event::listen(\App\Events\CommentCreated::class, \App\Listeners\RefreshDashboardListener::class);
        
        Event::listen(\App\Events\CommentUpdated::class, \App\Listeners\LogActivityListener::class);
        
        Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\LogActivityListener::class);
        Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        Event::listen(\App\Events\CommentDeleted::class, \App\Listeners\RefreshDashboardListener::class);
        
        Event::listen(\App\Events\CommentRestored::class, \App\Listeners\LogActivityListener::class);
        Event::listen(\App\Events\CommentRestored::class, \App\Listeners\UpdateDiscussionStatisticsListener::class);
        
        Event::listen(\App\Events\CommentPinned::class, \App\Listeners\LogActivityListener::class);
        Event::listen(\App\Events\CommentUnpinned::class, \App\Listeners\LogActivityListener::class);
        
        Event::listen(\App\Events\MentionDetected::class, \App\Listeners\NotifyMentionedUsersListener::class);

        // Meeting Events
        Event::listen(\App\Events\MeetingCreated::class, \App\Listeners\LogMeetingActivityListener::class);
        Event::listen(\App\Events\MeetingCreated::class, \App\Listeners\RefreshDashboardCalendarListener::class);
        Event::listen(\App\Events\MeetingCreated::class, \App\Listeners\DetectMeetingConflictListener::class);

        Event::listen(\App\Events\MeetingUpdated::class, \App\Listeners\LogMeetingActivityListener::class);
        Event::listen(\App\Events\MeetingUpdated::class, \App\Listeners\NotifyMeetingAttendeesListener::class);
        Event::listen(\App\Events\MeetingUpdated::class, \App\Listeners\RefreshDashboardCalendarListener::class);
        Event::listen(\App\Events\MeetingUpdated::class, \App\Listeners\DetectMeetingConflictListener::class);

        Event::listen(\App\Events\MeetingCancelled::class, \App\Listeners\LogMeetingActivityListener::class);
        Event::listen(\App\Events\MeetingCancelled::class, \App\Listeners\NotifyMeetingAttendeesListener::class);
        Event::listen(\App\Events\MeetingCancelled::class, \App\Listeners\RefreshDashboardCalendarListener::class);

        Event::listen(\App\Events\MeetingRescheduled::class, \App\Listeners\LogMeetingActivityListener::class);
        Event::listen(\App\Events\MeetingRescheduled::class, \App\Listeners\NotifyMeetingAttendeesListener::class);
        Event::listen(\App\Events\MeetingRescheduled::class, \App\Listeners\RefreshDashboardCalendarListener::class);

        Event::listen(\App\Events\MeetingInvitationSent::class, \App\Listeners\LogMeetingActivityListener::class);
        Event::listen(\App\Events\MeetingInvitationSent::class, \App\Listeners\NotifyMeetingAttendeesListener::class);

        Event::listen(\App\Events\MeetingAccepted::class, \App\Listeners\LogMeetingActivityListener::class);
        // Workflow Events
        Event::listen(\App\Events\WorkflowSubmitted::class, \App\Listeners\LogWorkflowActivity::class);
        Event::listen(\App\Events\WorkflowSubmitted::class, \App\Listeners\RefreshWorkflowMetrics::class);
        Event::listen(\App\Events\WorkflowSubmitted::class, \App\Listeners\UpdateDashboardMetrics::class);

        Event::listen(\App\Events\WorkflowApproved::class, \App\Listeners\LogWorkflowActivity::class);
        Event::listen(\App\Events\WorkflowApproved::class, \App\Listeners\NotifyRequester::class);
        Event::listen(\App\Events\WorkflowApproved::class, \App\Listeners\RefreshWorkflowMetrics::class);
        Event::listen(\App\Events\WorkflowApproved::class, \App\Listeners\UpdateDashboardMetrics::class);

        Event::listen(\App\Events\WorkflowRejected::class, \App\Listeners\LogWorkflowActivity::class);
        Event::listen(\App\Events\WorkflowRejected::class, \App\Listeners\NotifyRequester::class);
        Event::listen(\App\Events\WorkflowRejected::class, \App\Listeners\RefreshWorkflowMetrics::class);
        Event::listen(\App\Events\WorkflowRejected::class, \App\Listeners\UpdateDashboardMetrics::class);

        Event::listen(\App\Events\WorkflowReturned::class, \App\Listeners\LogWorkflowActivity::class);
        Event::listen(\App\Events\WorkflowReturned::class, \App\Listeners\NotifyRequester::class);
        Event::listen(\App\Events\WorkflowReturned::class, \App\Listeners\RefreshWorkflowMetrics::class);
        Event::listen(\App\Events\WorkflowReturned::class, \App\Listeners\UpdateDashboardMetrics::class);

        Event::listen(\App\Events\WorkflowCancelled::class, \App\Listeners\LogWorkflowActivity::class);
        Event::listen(\App\Events\WorkflowCancelled::class, \App\Listeners\RefreshWorkflowMetrics::class);
        Event::listen(\App\Events\WorkflowCancelled::class, \App\Listeners\UpdateDashboardMetrics::class);

        Event::listen(\App\Events\ApprovalAssigned::class, \App\Listeners\NotifyNextApprover::class);
    }
}

