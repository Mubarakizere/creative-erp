<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are served under the /api prefix and are stateless.
| Authentication is handled via Sanctum tokens.
|
*/

// Public API routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Financial Reporting
    Route::prefix('finance')->group(function () {
        Route::get('reports/profit-and-loss', [\App\Http\Controllers\Finance\FinancialReportController::class, 'profitAndLoss']);
        Route::get('reports/balance-sheet', [\App\Http\Controllers\Finance\FinancialReportController::class, 'balanceSheet']);
        Route::get('reports/cash-flow', [\App\Http\Controllers\Finance\FinancialReportController::class, 'cashFlow']);
        
        Route::apiResource('budgets', \App\Http\Controllers\Finance\BudgetController::class)->only(['index', 'show']);
        
        Route::get('analytics', [\App\Http\Controllers\Finance\AnalyticsController::class, 'index']);
    });
    
    // Dashboards
    Route::prefix('dashboard')->group(function () {
        Route::get('executive', [\App\Http\Controllers\Dashboard\ExecutiveDashboardController::class, 'index']);
    });
    
    // Inventory
    Route::prefix('inventory')->group(function () {
        Route::apiResource('products', \App\Http\Controllers\Api\Inventory\ProductController::class);
        Route::apiResource('warehouses', \App\Http\Controllers\Api\Inventory\WarehouseController::class);
        
        Route::get('stock', [\App\Http\Controllers\Api\Inventory\InventoryController::class, 'index']);
        Route::post('transfer', [\App\Http\Controllers\Api\Inventory\InventoryController::class, 'transfer']);
        Route::post('adjust', [\App\Http\Controllers\Api\Inventory\InventoryController::class, 'adjust']);
    });
});
