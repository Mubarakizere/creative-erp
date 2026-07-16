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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
});
