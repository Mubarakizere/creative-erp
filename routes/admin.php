<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
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
});
