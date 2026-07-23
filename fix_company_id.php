<?php

$files = [
    'app/Http/Controllers/Admin/Procurement/SupplierController.php',
    'app/Http/Controllers/Admin/Procurement/PurchaseRequisitionController.php',
    'app/Http/Controllers/Admin/Procurement/SupplierQuotationController.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(
            "session('company_id') ?? auth()->user()->company_id;",
            "session('company_id') ?? auth()->user()->company_id ?? 1;",
            $content
        );
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
