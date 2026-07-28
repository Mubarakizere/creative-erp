<?php

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Services\ExportService;
use App\Services\ReportBuilderService;
use Illuminate\Support\Facades\DB;

try {
    echo "--- ASSET METRICS ---\n";
    
    $companyId = 1; // Assuming test data uses company 1
    
    $totalAssets = Asset::where('company_id', $companyId)->count();
    $assetValue = Asset::where('company_id', $companyId)->sum('purchase_cost');
    $netBookValue = Asset::where('company_id', $companyId)->sum('current_book_value');
    $accumulatedDepreciation = Asset::where('company_id', $companyId)->sum('accumulated_depreciation');
    
    // Monthly depreciation (Sum of depreciation generated in the current month)
    $monthlyDepreciation = AssetDepreciation::whereHas('asset', function($q) use ($companyId) {
        $q->where('company_id', $companyId);
    })->whereYear('period_date', now()->year)
      ->whereMonth('period_date', now()->month)
      ->sum('amount');

    echo "Total Assets: " . number_format($totalAssets) . "\n";
    echo "Asset Value: " . number_format($assetValue, 2) . "\n";
    echo "Net Book Value: " . number_format($netBookValue, 2) . "\n";
    echo "Accumulated Depreciation: " . number_format($accumulatedDepreciation, 2) . "\n";
    echo "Monthly Depreciation: " . number_format($monthlyDepreciation, 2) . "\n";

    echo "\n--- ASSETS BY CATEGORY ---\n";
    $byCategory = Asset::where('assets.company_id', $companyId)
        ->join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
        ->select('asset_categories.name', DB::raw('count(*) as count'), DB::raw('sum(current_book_value) as value'))
        ->groupBy('asset_categories.name')
        ->get();
    foreach ($byCategory as $cat) {
        echo "{$cat->name}: {$cat->count} assets | Value: " . number_format($cat->value, 2) . "\n";
    }

    echo "\n--- ASSETS BY DEPARTMENT ---\n";
    $byDepartment = Asset::where('assets.company_id', $companyId)
        ->leftJoin('departments', 'assets.department_id', '=', 'departments.id')
        ->select('departments.name', DB::raw('count(*) as count'), DB::raw('sum(current_book_value) as value'))
        ->groupBy('departments.name')
        ->get();
    foreach ($byDepartment as $dept) {
        echo "{$dept->name}: {$dept->count} assets | Value: " . number_format($dept->value, 2) . "\n";
    }

    echo "\n--- ASSETS BY BRANCH ---\n";
    $byBranch = Asset::where('assets.company_id', $companyId)
        ->leftJoin('branches', 'assets.branch_id', '=', 'branches.id')
        ->select('branches.name', DB::raw('count(*) as count'), DB::raw('sum(current_book_value) as value'))
        ->groupBy('branches.name')
        ->get();
    foreach ($byBranch as $branch) {
        echo "{$branch->name}: {$branch->count} assets | Value: " . number_format($branch->value, 2) . "\n";
    }

    echo "\n--- EXPORT TEST ---\n";
    // Check if ExportService can handle it
    $reportService = app(ReportBuilderService::class);
    $data = $reportService->build('asset_register', ['company_id' => $companyId]);
    
    // Convert data to simple array for export testing
    $exportData = [];
    foreach ($data as $item) {
        $exportData[] = [
            'Asset Number' => $item->asset_number,
            'Name' => $item->name,
            'Category' => $item->category->name ?? '',
            'Book Value' => $item->current_book_value
        ];
    }
    
    if (class_exists('App\Services\ExportService')) {
        $exportService = app(ExportService::class);
        
        $csvContent = $exportService->generateCsv(collect($exportData), ['Asset Number', 'Name', 'Category', 'Book Value']);
        echo "CSV generated successfully. Length: " . strlen($csvContent) . " bytes\n";
        
        // Assume PDF and Excel work if they exist, but let's just check method existence
        if (method_exists($exportService, 'generatePdf')) {
            echo "PDF generator method exists.\n";
        }
        if (method_exists($exportService, 'generateExcel')) {
            echo "Excel generator method exists.\n";
        }
    } else {
        echo "ExportService not found.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
