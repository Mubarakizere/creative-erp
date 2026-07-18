<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for the admin panel. They require authentication
| and active user status. All routes are prefixed with /admin.
|
*/

Route::middleware(['auth', 'check.status', 'track.activity'])->prefix('admin')->name('admin.')->group(function () {
    // Access Pending page — accessible to authenticated users without a role
    Route::get('/access-pending', function () {
        return view('admin.access-pending');
    })->name('access-pending');
});

Route::middleware(['auth', 'check.status', 'track.activity', 'ensure.role'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Global Search
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::patch('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/preferences', [\App\Http\Controllers\Admin\NotificationPreferenceController::class, 'index'])->name('preferences');
        Route::put('/preferences', [\App\Http\Controllers\Admin\NotificationPreferenceController::class, 'update'])->name('preferences.update');
        Route::patch('/{notification}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('/{notification}/mark-unread', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsUnread'])->name('mark-unread');
    });
    Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class)->only(['index', 'show', 'destroy']);


    // Companies
    Route::patch('/companies/{company}/restore', [CompanyController::class, 'restore'])->name('companies.restore')->withTrashed();
    Route::patch('/companies/{company}/activate', [CompanyController::class, 'activate'])->name('companies.activate');
    Route::patch('/companies/{company}/deactivate', [CompanyController::class, 'deactivate'])->name('companies.deactivate');
    Route::get('/companies/{company}/settings', [CompanyController::class, 'settings'])->name('companies.settings');
    Route::resource('companies', CompanyController::class);

    // Branches
    Route::patch('/branches/{branch}/restore', [BranchController::class, 'restore'])->name('branches.restore')->withTrashed();
    Route::patch('/branches/{branch}/activate', [BranchController::class, 'activate'])->name('branches.activate');
    Route::patch('/branches/{branch}/deactivate', [BranchController::class, 'deactivate'])->name('branches.deactivate');
    Route::resource('branches', BranchController::class);

    // Departments
    Route::get('/departments/branches/{company}', [DepartmentController::class, 'getBranches'])->name('departments.branches');
    Route::patch('/departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore')->withTrashed();
    Route::patch('/departments/{department}/activate', [DepartmentController::class, 'activate'])->name('departments.activate');
    Route::patch('/departments/{department}/deactivate', [DepartmentController::class, 'deactivate'])->name('departments.deactivate');
    Route::resource('departments', DepartmentController::class);

    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Users
    Route::get('/users/branches/{company}', [UserController::class, 'getBranches'])->name('users.branches');
    Route::get('/users/departments/{branch}', [UserController::class, 'getDepartments'])->name('users.departments');
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::patch('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')->withTrashed();
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class);

    // Clients
    Route::patch('/clients/{client}/restore', [\App\Http\Controllers\Admin\ClientController::class, 'restore'])->name('clients.restore')->withTrashed();
    Route::patch('/clients/{client}/activate', [\App\Http\Controllers\Admin\ClientController::class, 'activate'])->name('clients.activate');
    Route::patch('/clients/{client}/deactivate', [\App\Http\Controllers\Admin\ClientController::class, 'deactivate'])->name('clients.deactivate');
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);

    // Projects
    Route::patch('/projects/{project}/restore', [\App\Http\Controllers\Admin\ProjectController::class, 'restore'])->name('projects.restore')->withTrashed();
    Route::patch('/projects/{project}/close', [\App\Http\Controllers\Admin\ProjectController::class, 'close'])->name('projects.close');
    Route::patch('/projects/{project}/reopen', [\App\Http\Controllers\Admin\ProjectController::class, 'reopen'])->name('projects.reopen');
    Route::post('/projects/{project}/duplicate', [\App\Http\Controllers\Admin\ProjectController::class, 'duplicate'])->name('projects.duplicate');
    Route::get('/projects/{project}/timeline', [\App\Http\Controllers\Admin\ProjectController::class, 'timeline'])->name('projects.timeline');
    // Project Teams
    Route::prefix('projects/team')->name('projects.team.')->group(function () {
        Route::patch('/{team}/restore', [\App\Http\Controllers\Admin\ProjectTeamController::class, 'restore'])->name('restore')->withTrashed();
        Route::patch('/{team}/activate', [\App\Http\Controllers\Admin\ProjectTeamController::class, 'activate'])->name('activate');
        Route::patch('/{team}/deactivate', [\App\Http\Controllers\Admin\ProjectTeamController::class, 'deactivate'])->name('deactivate');
    });
    Route::resource('projects/team', \App\Http\Controllers\Admin\ProjectTeamController::class, [
        'names' => 'projects.team',
        'parameters' => ['team' => 'teamMember']
    ]);

    // Tasks
    Route::prefix('projects/tasks')->name('projects.tasks.')->group(function () {
        Route::patch('/{task}/restore', [\App\Http\Controllers\Admin\TaskController::class, 'restore'])->name('restore')->withTrashed();
        Route::post('/{task}/duplicate', [\App\Http\Controllers\Admin\TaskController::class, 'duplicate'])->name('duplicate');
    });
    Route::resource('projects/tasks', \App\Http\Controllers\Admin\TaskController::class, [
        'names' => 'projects.tasks'
    ]);

    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);

    // Milestones
    Route::prefix('milestones')->name('milestones.')->group(function () {
        Route::patch('/{milestone}/restore', [\App\Http\Controllers\Admin\MilestoneController::class, 'restore'])->name('restore')->withTrashed();
        Route::post('/{milestone}/duplicate', [\App\Http\Controllers\Admin\MilestoneController::class, 'duplicate'])->name('duplicate');
        Route::post('/{milestone}/assign-tasks', [\App\Http\Controllers\Admin\MilestoneController::class, 'assignTasks'])->name('assign-tasks');
        Route::delete('/{milestone}/tasks/{task}', [\App\Http\Controllers\Admin\MilestoneController::class, 'removeTask'])->name('remove-task');
        Route::get('/{milestone}/timeline', [\App\Http\Controllers\Admin\MilestoneController::class, 'timeline'])->name('timeline');
        Route::get('/{milestone}/activity', [\App\Http\Controllers\Admin\MilestoneController::class, 'activity'])->name('activity');
    });
    Route::resource('milestones', \App\Http\Controllers\Admin\MilestoneController::class);
    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/records/{module}', [\App\Http\Controllers\Admin\DocumentController::class, 'getRecords'])->name('records');
        Route::get('/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('download');
    });
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);

    // Document Categories
    Route::resource('document-categories', \App\Http\Controllers\Admin\DocumentCategoryController::class);

    // Comments & Discussions
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/{comment}/pin', [\App\Http\Controllers\CommentController::class, 'pin'])->name('pin');
        Route::post('/{comment}/unpin', [\App\Http\Controllers\CommentController::class, 'unpin'])->name('unpin');
        Route::patch('/{comment}/restore', [\App\Http\Controllers\CommentController::class, 'restore'])->name('restore')->withTrashed();
    });
    Route::resource('comments', \App\Http\Controllers\CommentController::class)->only(['store', 'update', 'destroy']);

    // Time Tracking
    Route::prefix('time-tracking')->name('time-tracking.')->group(function () {
        Route::get('/timesheet', [\App\Http\Controllers\Admin\TimeEntryController::class, 'timesheet'])->name('timesheet');
        Route::get('/reports', [\App\Http\Controllers\Admin\TimeEntryController::class, 'reports'])->name('reports');
        
        // Timer
        Route::post('/timer/start', [\App\Http\Controllers\Admin\TimeEntryController::class, 'startTimer'])->name('timer.start');
        Route::patch('/timer/{timeEntry}/stop', [\App\Http\Controllers\Admin\TimeEntryController::class, 'stopTimer'])->name('timer.stop');
        Route::patch('/timer/{timeEntry}/pause', [\App\Http\Controllers\Admin\TimeEntryController::class, 'pauseTimer'])->name('timer.pause');
        Route::patch('/timer/{timeEntry}/resume', [\App\Http\Controllers\Admin\TimeEntryController::class, 'resumeTimer'])->name('timer.resume');
    });
    Route::resource('time-tracking', \App\Http\Controllers\Admin\TimeEntryController::class)->parameters(['time-tracking' => 'timeEntry']);

    // Calendar
    Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [\App\Http\Controllers\Admin\CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/agenda', [\App\Http\Controllers\Admin\CalendarController::class, 'agenda'])->name('calendar.agenda');
    Route::get('/calendar/upcoming', [\App\Http\Controllers\Admin\CalendarController::class, 'upcoming'])->name('calendar.upcoming');

    // Meetings
    Route::patch('/meetings/{meeting}/restore', [\App\Http\Controllers\Admin\MeetingController::class, 'restore'])->name('meetings.restore')->withTrashed();
    Route::patch('/meetings/{meeting}/cancel', [\App\Http\Controllers\Admin\MeetingController::class, 'cancel'])->name('meetings.cancel');
    Route::patch('/meetings/{meeting}/reschedule', [\App\Http\Controllers\Admin\MeetingController::class, 'reschedule'])->name('meetings.reschedule');
    Route::post('/meetings/{meeting}/invite', [\App\Http\Controllers\Admin\MeetingController::class, 'invite'])->name('meetings.invite');
    Route::patch('/meetings/{meeting}/respond', [\App\Http\Controllers\Admin\MeetingController::class, 'respond'])->name('meetings.respond');
    Route::resource('meetings', \App\Http\Controllers\Admin\MeetingController::class);

    // Workflows & Approvals
    Route::resource('workflows', \App\Http\Controllers\Admin\WorkflowController::class);
    Route::get('approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{approval}/action', [\App\Http\Controllers\Admin\ApprovalController::class, 'action'])->name('approvals.action');

    // Announcements
    Route::patch('announcements/{announcement}/publish', [\App\Http\Controllers\Admin\AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::patch('announcements/{announcement}/unpublish', [\App\Http\Controllers\Admin\AnnouncementController::class, 'unpublish'])->name('announcements.unpublish');
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
});

