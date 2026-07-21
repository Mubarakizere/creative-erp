<?php

namespace App\Services\Finance;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;

class StatementService
{
    public function generateCustomerStatement(Client $client): array
    {
        $invoices = Invoice::where('client_id', $client->id)->get();
        $payments = Payment::where('client_id', $client->id)->get();
        
        // This is a simplified statement generation.
        // Usually, we'd interleave these by date to create a running ledger.
        
        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $payments->sum('amount');
        $outstandingBalance = $totalInvoiced - $totalPaid;
        
        return [
            'client' => $client,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding_balance' => max(0, $outstandingBalance),
            'invoices' => $invoices,
            'payments' => $payments,
        ];
    }
}
