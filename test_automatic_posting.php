<?php

use App\Models\AccountType;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Client;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Services\Finance\InvoiceService;
use App\Services\Finance\PaymentService;
use App\Services\Finance\RefundService;
use App\Services\Finance\JournalService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    auth()->loginUsingId(1);
    $company = Company::first();
$companyId = $company->id;

echo "\n--- Automatic Posting Tests ---\n\n";

// 1. Prepare system accounts
$assetType = AccountType::where('company_id', $companyId)->where('name', 'Current Asset')->first();
$arAccount = ChartOfAccount::firstOrCreate(
    ['company_id' => $companyId, 'name' => 'Accounts Receivable'],
    ['code' => '1200', 'account_type_id' => $assetType->id]
);
$arAccount->update(['is_system' => true]);

$salesAccount = ChartOfAccount::where('company_id', $companyId)->where('name', 'like', '%Sales%')->firstOrFail();
$salesAccount->update(['is_system' => true]);

$bankAccount = ChartOfAccount::where('company_id', $companyId)->where('name', 'like', '%Bank%')->firstOrFail();
$bankAccount->update(['is_system' => true]);

echo "System accounts prepared.\n";

// 2. Prepare Client
$branch = Branch::firstOrCreate(['company_id' => $companyId], ['name' => 'HQ', 'code' => 'HQ-1']);

$client = Client::firstOrCreate(
    ['company_id' => $companyId, 'email' => 'client@example.com'],
    [
        'name' => 'Test Client', 
        'status' => 'Active', 
        'branch_id' => $branch->id,
        'client_type' => 'Company',
        'phone' => '1234567890'
    ]
);

$invoiceService = app(InvoiceService::class);
$paymentService = app(PaymentService::class);
$refundService = app(RefundService::class);
$journalService = app(JournalService::class);

// 3. Create Invoice
$invoice = $invoiceService->createInvoice(
    [
        'company_id' => $companyId,
        'client_id' => $client->id,
        'issue_date' => now(),
        'due_date' => now()->addDays(15),
    ],
    [
        ['description' => 'Consulting Services', 'quantity' => 10, 'unit_price' => 150]
    ]
);
echo "1. Invoice {$invoice->invoice_number} created for $1500.\n";

// Verify Journal
$invJournal = Journal::where('reference_number', $invoice->invoice_number)->first();
if ($invJournal && $invJournal->status === 'Posted') {
    echo "   [PASS] Automatic Journal generated and posted for Invoice.\n";
} else {
    echo "   [FAIL] Automatic Journal missing or not posted for Invoice.\n";
}

// 4. Create Payment
$payment = $paymentService->processPayment(
    [
        'company_id' => $companyId,
        'client_id' => $client->id,
        'amount' => 1500,
        'payment_date' => now(),
    ],
    [
        ['invoice_id' => $invoice->id, 'amount' => 1500]
    ]
);
echo "2. Payment {$payment->payment_number} created for $1500.\n";

$payJournal = Journal::where('reference_number', $payment->payment_number)->first();
if ($payJournal && $payJournal->status === 'Posted') {
    echo "   [PASS] Automatic Journal generated and posted for Payment.\n";
} else {
    echo "   [FAIL] Automatic Journal missing or not posted for Payment.\n";
}

// 5. Create Refund
$refund = $refundService->processRefund([
    'company_id' => $companyId,
    'client_id' => $client->id,
    'payment_id' => $payment->id,
    'amount' => 500,
    'reason' => 'Customer requested partial refund',
]);
echo "3. Refund {$refund->refund_number} created for $500.\n";

$refJournal = Journal::where('reference_number', $refund->refund_number)->first();
if ($refJournal && $refJournal->status === 'Posted') {
    echo "   [PASS] Automatic Journal generated and posted for Refund.\n";
} else {
    echo "   [FAIL] Automatic Journal missing or not posted for Refund.\n";
}

    echo "\nDone.\n";
} catch (\Throwable $e) {
    echo "\n[ERROR] Exception caught:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
