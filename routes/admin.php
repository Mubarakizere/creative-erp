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
    Route::get('/dashboard/executive', [\App\Http\Controllers\Dashboard\ExecutiveDashboardController::class, 'index'])->name('dashboard.executive');
    
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

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/builder', [\App\Http\Controllers\Admin\ReportController::class, 'builder'])->name('builder');
        Route::post('/preview', [\App\Http\Controllers\Admin\ReportController::class, 'preview'])->name('preview');
        Route::post('/{reportTemplate}/favorite', [\App\Http\Controllers\Admin\ReportController::class, 'favorite'])->name('favorite');
        Route::post('/{reportTemplate}/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });
    Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class)->parameters(['reports' => 'reportTemplate']);

    // CRM
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::patch('/leads/{lead}/restore', [\App\Http\Controllers\Admin\LeadController::class, 'restore'])->name('leads.restore')->withTrashed();
        Route::post('/leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convert'])->name('leads.convert');
        Route::get('/leads/{lead}/timeline', [\App\Http\Controllers\Admin\LeadController::class, 'timeline'])->name('leads.timeline');
        Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class);

        Route::patch('/accounts/{account}/restore', [\App\Http\Controllers\Admin\AccountController::class, 'restore'])->name('accounts.restore')->withTrashed();
        Route::get('/accounts/{account}/timeline', [\App\Http\Controllers\Admin\AccountController::class, 'timeline'])->name('accounts.timeline');
        Route::resource('accounts', \App\Http\Controllers\Admin\AccountController::class);

        Route::patch('/contacts/{contact}/restore', [\App\Http\Controllers\Admin\ContactController::class, 'restore'])->name('contacts.restore')->withTrashed();
        Route::get('/contacts/{contact}/timeline', [\App\Http\Controllers\Admin\ContactController::class, 'timeline'])->name('contacts.timeline');
        Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class);

        Route::patch('/opportunities/{opportunity}/restore', [\App\Http\Controllers\Admin\OpportunityController::class, 'restore'])->name('opportunities.restore')->withTrashed();
        Route::get('/opportunities/kanban', [\App\Http\Controllers\Admin\OpportunityController::class, 'kanban'])->name('opportunities.kanban');
        Route::patch('/opportunities/{opportunity}/kanban-update', [\App\Http\Controllers\Admin\OpportunityController::class, 'updateKanbanStage'])->name('opportunities.kanban.update');
        Route::resource('opportunities', \App\Http\Controllers\Admin\OpportunityController::class);

        Route::resource('pipelines', \App\Http\Controllers\Admin\PipelineController::class);
        
        Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class);

        // Sales Documents (Quotations)
        Route::patch('/quotations/{quotation}/restore', [\App\Http\Controllers\QuotationController::class, 'restore'])->name('quotations.restore')->withTrashed();
        Route::post('/quotations/{quotation}/duplicate', [\App\Http\Controllers\QuotationController::class, 'duplicate'])->name('quotations.duplicate');
        Route::post('/quotations/{quotation}/submit', [\App\Http\Controllers\QuotationController::class, 'submit'])->name('quotations.submit');
        Route::post('/quotations/{quotation}/approve', [\App\Http\Controllers\QuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('/quotations/{quotation}/reject', [\App\Http\Controllers\QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('/quotations/{quotation}/export', [\App\Http\Controllers\QuotationController::class, 'export'])->name('quotations.export');
        Route::resource('quotations', \App\Http\Controllers\QuotationController::class);
    });

    // Finance (Payments & Receivables)
    Route::prefix('finance')->name('finance.')->group(function () {
        // Accounting Foundation
        Route::prefix('accounting')->name('accounting.')->group(function () {
            Route::resource('chart-of-accounts', \App\Http\Controllers\Finance\Accounting\ChartOfAccountController::class);
            
            Route::post('journals/{journal}/post', [\App\Http\Controllers\Finance\Accounting\JournalController::class, 'post'])->name('journals.post');
            Route::resource('journals', \App\Http\Controllers\Finance\Accounting\JournalController::class)->except(['edit', 'update', 'destroy']);
            
            Route::get('ledger', [\App\Http\Controllers\Finance\Accounting\LedgerController::class, 'index'])->name('ledger.index');
            
            Route::post('fiscal-periods/years', [\App\Http\Controllers\Finance\Accounting\FiscalPeriodController::class, 'storeYear'])->name('fiscal-periods.years.store');
            Route::patch('fiscal-periods/years/{year}/close', [\App\Http\Controllers\Finance\Accounting\FiscalPeriodController::class, 'closeYear'])->name('fiscal-periods.years.close');
            Route::post('fiscal-periods/periods', [\App\Http\Controllers\Finance\Accounting\FiscalPeriodController::class, 'storePeriod'])->name('fiscal-periods.periods.store');
            Route::patch('fiscal-periods/periods/{period}/close', [\App\Http\Controllers\Finance\Accounting\FiscalPeriodController::class, 'closePeriod'])->name('fiscal-periods.periods.close');
            Route::get('fiscal-periods', [\App\Http\Controllers\Finance\Accounting\FiscalPeriodController::class, 'index'])->name('fiscal-periods.index');
        });

        // Finance Settings
        Route::get('settings', [\App\Http\Controllers\Finance\FinanceSettingsController::class, 'index'])->name('settings');
        Route::post('settings/payment-methods', [\App\Http\Controllers\Finance\FinanceSettingsController::class, 'storePaymentMethod'])->name('payment-methods.store');
        Route::delete('settings/payment-methods/{id}', [\App\Http\Controllers\Finance\FinanceSettingsController::class, 'destroyPaymentMethod'])->name('payment-methods.destroy');
        Route::post('settings/bank-accounts', [\App\Http\Controllers\Finance\FinanceSettingsController::class, 'storeBankAccount'])->name('bank-accounts.store');
        Route::delete('settings/bank-accounts/{id}', [\App\Http\Controllers\Finance\FinanceSettingsController::class, 'destroyBankAccount'])->name('bank-accounts.destroy');

        Route::patch('/invoices/{invoice}/issue', [\App\Http\Controllers\Finance\InvoiceController::class, 'issue'])->name('invoices.issue');
        Route::patch('/invoices/{invoice}/cancel', [\App\Http\Controllers\Finance\InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::resource('invoices', \App\Http\Controllers\Finance\InvoiceController::class);
        
        Route::resource('payments', \App\Http\Controllers\Finance\PaymentController::class)->except(['edit', 'update']);
        
        Route::post('/credit-notes/{creditNote}/apply', [\App\Http\Controllers\Finance\CreditNoteController::class, 'apply'])->name('credit-notes.apply');
        Route::resource('credit-notes', \App\Http\Controllers\Finance\CreditNoteController::class)->except(['edit', 'update']);
        
        Route::resource('refunds', \App\Http\Controllers\Finance\RefundController::class)->except(['edit', 'update']);
        Route::resource('bank-accounts', \App\Http\Controllers\Finance\BankAccountController::class);
        
        Route::get('/statements/{client}', [\App\Http\Controllers\Finance\CustomerStatementController::class, 'show'])->name('statements.show');
        
        // Financial Reporting & Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('profit-and-loss', [\App\Http\Controllers\Finance\FinancialReportController::class, 'profitAndLoss'])->name('profit-and-loss');
            Route::get('balance-sheet', [\App\Http\Controllers\Finance\FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('cash-flow', [\App\Http\Controllers\Finance\FinancialReportController::class, 'cashFlow'])->name('cash-flow');
        });
        
        Route::resource('budgets', \App\Http\Controllers\Finance\BudgetController::class);
        
        Route::get('analytics', [\App\Http\Controllers\Finance\AnalyticsController::class, 'index'])->name('analytics');
    });
});
