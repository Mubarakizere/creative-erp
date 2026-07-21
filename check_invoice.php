<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$inv = App\Models\Invoice::find(1);
if ($inv) {
    echo "Invoice #1:\n";
    echo "  total_amount: " . $inv->total_amount . "\n";
    echo "  subtotal: " . $inv->subtotal . "\n";
    echo "  tax_total: " . $inv->tax_total . "\n";
    echo "  discount_total: " . $inv->discount_total . "\n";
    echo "  paid_amount: " . $inv->paid_amount . "\n";
    echo "  balance_due: " . $inv->balance_due . "\n";
    echo "  status: " . $inv->status . "\n";
    
    // Check items
    echo "\nItems:\n";
    foreach ($inv->items as $item) {
        echo "  - " . $item->description . ": qty=" . $item->quantity . " x price=" . $item->unit_price . " = " . ($item->quantity * $item->unit_price) . " | tax=" . $item->tax_amount . " discount=" . $item->discount_amount . " total=" . $item->total_amount . "\n";
    }
    
    // Check allocations
    echo "\nAllocations:\n";
    $allocs = $inv->allocations()->with('payment')->get();
    foreach ($allocs as $alloc) {
        echo "  - Payment #" . ($alloc->payment ? $alloc->payment->payment_number : 'N/A') . ": allocated=" . $alloc->amount_allocated . "\n";
    }
    
    // Recalculate what balance should be
    $expectedTotal = $inv->subtotal - $inv->discount_total + $inv->tax_total;
    $paidViaAllocations = $allocs->sum('amount_allocated');
    echo "\nExpected total_amount: " . $expectedTotal . "\n";
    echo "Actual total_amount: " . $inv->total_amount . "\n";
    echo "Sum of allocations: " . $paidViaAllocations . "\n";
    echo "Expected balance_due: " . ($expectedTotal - $paidViaAllocations) . "\n";
    echo "Actual balance_due: " . $inv->balance_due . "\n";
} else {
    echo "Invoice #1 not found\n";
}
