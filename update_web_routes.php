<?php

$routesPath = 'routes/admin.php';
$content = file_get_contents($routesPath);

$routesToInject = <<<'EOT'

    // Procurement
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\Admin\Procurement\SupplierController::class);
        Route::post('requisitions/{requisition}/approve', [\App\Http\Controllers\Admin\Procurement\PurchaseRequisitionController::class, 'approve'])->name('requisitions.approve');
        Route::resource('requisitions', \App\Http\Controllers\Admin\Procurement\PurchaseRequisitionController::class);
        Route::resource('rfqs', \App\Http\Controllers\Admin\Procurement\SupplierQuotationController::class);
    });
EOT;

if (!str_contains($content, "prefix('procurement')")) {
    $pos = strrpos($content, "});");
    if ($pos !== false) {
        $content = substr_replace($content, $routesToInject . "\n", $pos, 0);
        file_put_contents($routesPath, $content);
        echo "Added Procurement routes to admin.php\n";
    }
} else {
    echo "Routes already exist\n";
}
