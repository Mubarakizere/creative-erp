<?php

$routesPath = 'routes/admin.php';
$content = file_get_contents($routesPath);

if (!str_contains($content, 'requisitions/{requisition}/compare')) {
    $routesToInject = <<<'EOT'
        Route::get('requisitions/{requisition}/compare', [\App\Http\Controllers\Admin\Procurement\PurchaseRequisitionController::class, 'compare'])->name('requisitions.compare');
        Route::post('requisitions/{requisition}/accept/{quotation}', [\App\Http\Controllers\Admin\Procurement\PurchaseRequisitionController::class, 'acceptQuotation'])->name('requisitions.accept');
        
        Route::post('pos/{po}/approve', [\App\Http\Controllers\Admin\Procurement\PurchaseOrderController::class, 'approve'])->name('pos.approve');
        Route::resource('pos', \App\Http\Controllers\Admin\Procurement\PurchaseOrderController::class);
        
        Route::resource('receipts', \App\Http\Controllers\Admin\Procurement\GoodsReceiptController::class);
EOT;

    $pos = strpos($content, "Route::resource('requisitions'");
    if ($pos !== false) {
        $content = substr_replace($content, $routesToInject . "\n        ", $pos, 0);
        file_put_contents($routesPath, $content);
        echo "Added Phase 2 routes to admin.php\n";
    }
}
